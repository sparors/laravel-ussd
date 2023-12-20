# Laravel Ussd

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

Build Ussd (Unstructured Supplementary Service Data) applications with laravel without breaking a sweat.

## Installation

You can install the package via composer:

``` bash
composer require sparors/laravel-ussd:^3.0
```

Laravel Ussd provides zero configuration out of the box. To publish the config, run the vendor publish command:

``` bash
php artisan vendor:publish --provider="Sparors\Ussd\UssdServiceProvider" --tag=ussd-config
```

## Usage

### Context

The context of the ussd contains vital data required to succcessfully run a ussd application. It require 3 major input and any addition one you may choose to provide.

SID: refers to a unique id for every session.

GID: refers to a group id that is common to a user accross session. This is ussually just the phone number or msisdn.

Input: is the last input the user entered.

Aside these, you may choose to pass addition information like network and phone number if you may need them.

``` php
<?php

use Sparors\Ussd\Context;

Context::create(
    request('sessionID'),
    request('phoneNumber'),
    request('input')
)->with([
    'network' => request('network'),
    'phone_number' => request('phoneNumber')
])
```

### Record

Ussd record provides a simple way to save data as your application runs.

``` php
<?php

use Sparors\Ussd\Record;

$record = App::make(Record::class);

$record->set('name', 'Isaac');
```

### Dependency Injection

You can inject record and context into ussd application to make use of them when needed.

### States

We provide an artisan command which allows you to quickly create new states. State should have one method render which returns `Sparors\Ussd\Menu`

``` bash
php artisan ussd:state WelcomeState
```

States help build ussd menus that users interupt with. `Sparors\Ussd\Menu` provides a fluent API to easily create menus. `Sparors\Ussd\Attributes\Transition` attributes help to define how to connect one state to another while `Sparors\Ussd\Attributes\Terminate` help know the final state.

``` php
<?php

namespace App\Ussd\States;

use Sparors\Ussd\Menu;
use Sparors\Ussd\Context;
use Sparors\Ussd\Decisions\Equal;
use Sparors\Ussd\Contracts\State;
use Sparors\Ussd\Attributes\Transition;

#[Transition(MakePaymentAction::class, new Equal(1))]
#[Transition(InvalidInputState::class, new Fallback)]
class WelcomeState implements State
{
    public function render(Context $context): Menu
    {
       return Menu::build()
            ->text('Welcome To Laravel USSD')
            ->lineBreak(2)
            ->line('Select an option . ' . $context->get('network'))
            ->listing([
                'Airtime Topup',
                'Data Bundle',
                'TV Subscription',
                'ECG/GWCL',
                'Talk To Us'
            ])
            ->lineBreak(2)
            ->text('Powered by Sparors');
    }
}
```

The first state should implement `Sparors\Ussd\Contracts\InitialState` instead of the generic state.

Due to some limitation with PHP 8.0, you can not pass class instance to attributes. So to other come this limitation, you can pass an array with the full class path as the first element and the rest should be argument required.

``` php
#[Transition(MakePaymentAction::class, [Equal::class, 1])]
#[Transition(InvalidInputState::class, Fallback::class)]
class WelcomeState implements State {}
```

Final States should not have `Transition` but rather `Terminate`.

``` php
#[Terminate]
class GoodByeState implements State
{
    public function render(Record $record): Menu
    {
       return Menu::build()->text('Bye bye ' . $record->get('name'));
    }
}
```

### Actions

We provide a ussd artisan command which allows you to quickly create new actions. Action should have one method execute which returns a string.

``` bash
php artisan ussd:action MakePaymentAction
```

Actions should return a string which is the full qualified path to a state or another action.

``` php
<?php

namespace App\Http\Ussd\Actions;

use Sparors\Ussd\Contracts\Action;
use App\Http\Ussd\States\PaymentSuccess;
use App\Http\Ussd\States\PaymentError;

class MakePayment extends Action
{
    public function execute(Record $record): string
    {
        $response = Http::post('/payment', [
            'phone_number' => $record->phoneNumber
        ]);

        if ($response->ok()) {
            return PaymentSuccess::class;
        }

        return PaymentError::class;
    }
}
```

### Running a ussd Application

``` php
<?php

namespace App\Http\Controllers;

use Sparors\Ussd\Ussd;
use Sparors\Ussd\Context;
use App\Ussd\States\WelcomeState;

class UssdController extends Controller
{
    public function index()
    {
        return Ussd::build(
                    Context::create(
                        request('sessionID'),
                        request('phoneNumber'),
                        request('input')
                    )->with([
                        'network' => request('network'),
                        'phone_number' => request('phoneNumber')
                    ])
                )
                ->useInitialState(WelcomeState::class)
                ->run();
    }
}
```

### Simplifying USSD with configurator

You can use configurator to simplify repetitive parts of your application so they can be shared easily. Just implement and `Sparors\Ussd\Contracts\Configurator` interface and use it in your machine.
```php
<?php

use Sparors\Ussd\Contracts\Configurator;

// Creating a configurator in eg. App\Http\Ussd\Configurators\Nsano.php
class Nsano implements Configurator
{
    public function configure(Ussd $ussd): void
    {
        $ussd->setResponse(function (string $message, int $terminating) {
            return [
                'USSDResp' => [
                    'action' => $termination ? 'prompt': 'input',
                    'menus' => '',
                    'title' => $message
                ]
            ];
        });
    }
}
?>
```
```php
<?php

namespace App\Http\Controllers;

use Sparors\Ussd\Facades\Ussd;
use App\Http\Ussd\States\Welcome;
use App\Http\Ussd\Configurators\Nsano'

// Using it in a controller
class UssdController extends Controller
{
    public function index()
    {
        return Ussd::build(Context::create('1', '2', '3'))
            ->useConfigurator(Nsano::class)
            ->useInitialState(Welcome::class)
            ->run();
    }
}
?>
```

### Documentation

You'll find the documentation on [https://sparors.github.io/ussd-docs](https://sparors.github.io/ussd-docs/).


### Testing

``` bash
$ vendor/bin/phpunit
```

### Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

### Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

### Security

If you discover any security related issues, please email isaacsai030@gmail.com instead of using the issue tracker.

### Credits

- [Sparors Inc][link-author]
- [All Contributors][link-contributors]

### License

MIT. Please see the [license file](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/sparors/laravel-ussd.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/sparors/laravel-ussd.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/sparors/laravel-ussd/master.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/sparors/laravel-ussd
[link-downloads]: https://packagist.org/packages/sparors/laravel-ussd
[link-travis]: https://travis-ci.com/sparors/laravel-ussd
[link-author]: https://github.com/sparors
[link-contributors]: ../../contributors
