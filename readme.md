# Laravel Ussd

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

Build Ussd (Unstructured Supplementary Service Data) applications with laravel without breaking a sweat.

## Installation

You can install the package via composer:

``` bash
composer require sparors/laravel-ussd
```

Laravel Ussd provides zero configuration out of the box. To publish the config, run the vendor publish command:

``` bash
php artisan vendor:publish --provider="Sparors\Ussd\UssdServiceProvider" --tag=ussd-config
```

## Usage

### Creating States

We provide a ussd artisan command which allows you to quickly create new states.

``` bash
php artisan ussd:state Welcome
```

### Creating Nested States

Linux/Unix

``` bash
php artisan ussd:state Airtime/Welcome
```

Windows

``` bash
php artisan ussd:state Airtime\Welcome
```

Welcome state class generated

``` php
<?php

namespace App\Http\Ussd\States;

use Sparors\Ussd\State;

class Welcome extends State
{
    protected function beforeRendering(): void
    {
       //
    }

    protected function afterRendering(string $argument): void
    {
        //
    }
}
```

### Creating Actions

> Available from **v2.0.0**

We provide a ussd artisan command which allows you to quickly create new actions.

``` bash
php artisan ussd:action MakePayment
```

MakePayment action class generated

``` php
<?php

namespace App\Http\Ussd\Actions;

use Sparors\Ussd\Action;

class MakePayment extends Action
{
    public function run(): string
    {
        return ''; // The state after this
    }
}
```

Run your logic and return the next state's fully qualified class name

``` php
<?php

namespace App\Http\Ussd\Actions;

use Sparors\Ussd\Action;
use App\Http\Ussd\States\PaymentSuccess;
use App\Http\Ussd\States\PaymentError;

class MakePayment extends Action
{
    public function run(): string
    {
        $response = Http::post('/payment', [
            'phone_number' => $this->record->phoneNumber
        ]);

        if ($response->ok()) {
            return PaymentSuccess::class;
        }

        return PaymentError::class;
    }
}
```

### Creating Menus

Add your menu to the beforeRendering method

``` php
<?php

namespace App\Http\Ussd\States;

use Sparors\Ussd\State;

class Welcome extends State
{
    protected function beforeRendering(): void
    {
        $name = $this->record->name;

        $this->menu->text('Welcome To Laravel USSD')
            ->lineBreak(2)
            ->line('Select an option')
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

    protected function afterRendering(string $argument): void
    {
        //
    }
}
```

### Linking States with Decisions

Add your decision to the afterRendering method and link them with states

``` php
<?php

namespace App\Http\Ussd\States;

use App\Http\Ussd\States\GetRecipientNumber;
use App\Http\Ussd\States\MaintenanceMode;
use App\Http\Ussd\States\Error;
use Sparors\Ussd\State;

class Welcome extends State
{
    protected function beforeRendering(): void
    {
       $this->menu->text('Welcome To Laravel Ussd')
            ->lineBreak(2)
            ->line('Select an option')
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

    protected function afterRendering(string $argument): void
    {
        // If input is equal to 1, 2, 3, 4 or 5, render the appropriate state
        $this->decision->equal('1', GetRecipientNumber::class)
                       ->between(2, 5, MaintenanceMode::class)
                       ->any(Error::class);
    }
}
```

### Setting Initial State

Import the welcome state class and pass it to the setInitialState method

``` php
<?php

namespace App\Http\Controllers;

use Sparors\Ussd\Facades\Ussd;
use App\Http\Ussd\States\Welcome;

class UssdController extends Controller
{
    public function index()
    {
        $ussd = Ussd::machine()
            ->setFromRequest([
                'network',
                'phone_number' => 'msisdn',
                'sessionId' => 'UserSessionID',
                'input' => 'msg'
            ])
          ->setInitialState(Welcome::class)
          ->setResponse(function (string $message, string $action) {
                return [
                    'USSDResp' => [
                        'action' => $action,
                        'menus' => '',
                        'title' => $message
                    ]
                ];
            });

        return response()->json($ussd->run());
    }
}
```

### Simplifying machine with configurator

> Available from **v2.5.0**

You can use configurator to simplify repetitive parts of your application so they can be shared easily. Just implement and `Sparors\Ussd\Contracts\Configurator` interface and use it in your machine.
```php
<?php

use Sparors\Ussd\Contracts\Configurator;

// Creating a configurator in eg. App\Http\Ussd\Configurators\Nsano.php
class Nsano implements Configurator
{
    public function configure(Machine $machine): void
    {
        $machine->setFromRequest([
                'network',
                'phone_number' => 'msisdn',
                'sessionId' => 'UserSessionID',
                'input' => 'msg'
        ])->setResponse(function (string $message, string $action) {
            return [
                'USSDResp' => [
                    'action' => $action,
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
        $ussd = Ussd::machine()
            ->useConfigurator(Nsano::class)
            ->setInitialState(Welcome::class);

        return response()->json($ussd->run());
    }
}
?>
```


### Running the application

You can use the development server the ships with Laravel by running, from the project root:

``` bash
php artisan serve
```
You can visit [http://localhost:8000](http://localhost:8000) to see the application in action.

Enjoy!!!

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
