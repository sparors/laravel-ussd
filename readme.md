# Laravel Ussd

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-github]][link-github]

Build Ussd (Unstructured Supplementary Service Data) applications with laravel without breaking a sweat.

## Installation

You can install the package via composer:

``` bash
composer require sparors/laravel-ussd:^3
```

For older version use

``` bash
composer require sparors/laravel-ussd:^2
```

Laravel Ussd provides zero configuration out of the box. To publish the config, run the vendor publish command:

``` bash
php artisan vendor:publish --provider="Sparors\Ussd\UssdServiceProvider" --tag=ussd-config
```

## Usage

For older version look here: [V2 README](./v2.readme.md)

### Creating USSD menus

```php
<?php

namespace App\Ussd\States;

use App\Ussd\Actions\TransferAccountAction;
use Sparors\Ussd\Attributes\Paginate;
use Sparors\Ussd\Attributes\Transition;
use Sparors\Ussd\Context;
use Sparors\Ussd\Contracts\State;
use Sparors\Ussd\Decisions\Equal;
use Sparors\Ussd\Decisions\Fallback;
use Sparors\Ussd\Decisions\In;
use Sparors\Ussd\Menu;
use Sparors\Ussd\Record;
use Sparors\Ussd\Traits\WithPagination;

#[Transition(to: TransferAccountAction::class, match: new Equal(1))]
#[Transition(to: TransferAmountState::class, match: new In(2, 3), callback: [self::class, 'setTransferType'])]
#[Transition(to: NewAccountNameState::class, match: new Equal(4))]
#[Transition(to: HelplineState::class, match: new Equal(5))]
#[Transition(to: InvalidInputState::class, match: new Fallback())]
#[Paginate(next: new Equal('#'), previous: new Equal('0'))]
class CustomerMenuState implements State
{
    use WithPagination;

    public function render(): Menu
    {
        return Menu::build()
            ->line('Banc')
            ->listing($this->getItems(), page: $this->currentPage(), perPage: $this->perPage())
            ->when($this->hasPreviousPage(), fn (Menu $menu) => $menu->line('0. Previous'))
            ->when($this->hasNextPage(), fn (Menu $menu) => $menu->line('#. Next'))
            ->text('Powered by Sparors');
    }

    public function setTransferType(Context $context, Record $record)
    {
        $transferType = '2' === $context->input() ? 'deposit' : 'withdraw';

        $record->set('transfer_type', $transferType);
    }

    public function getItems(): array
    {
        return [
            'Transfer',
            'Deposit',
            'Withdraw',
            'New Account',
            'Helpline',
        ];
    }

    public function perPage(): int
    {
        return 3;
    }
}
```

An example of a final state

``` php
<?php

namespace App\Ussd\States;

use Sparors\Ussd\Attributes\Terminate;
use Sparors\Ussd\Contracts\State;
use Sparors\Ussd\Menu;
use Sparors\Ussd\Record;

#[Terminate]
class GoodByeState implements State
{
    public function render(Record $record): Menu
    {
       return Menu::build()->text('Bye bye ' . $record->get('name'));
    }
}
```

Due to some limitation with PHP 8.0, you can not pass class instance to attributes. So to overcome this limitation, you can pass an array with the full class path as the first element and the rest should be argument required. eg.

``` php
<?php

namespace App\Ussd\States;

use Sparors\Ussd\Attributes\Transition;
use Sparors\Ussd\Contracts\State;
use Sparors\Ussd\Menu;

#[Transition(MakePaymentAction::class, [Equal::class, 1])]
#[Transition(InvalidInputState::class, Fallback::class)]
class WelcomeState implements State
{
    public function render(): Menu
    {
       return Menu::build()->text('Welcome');
    }
}
```

### Building USSD

```php
<?php

namespace App\Http\Controllers;

use App\Ussd\Actions\MenuAction;
use App\Ussd\Responses\AfricasTalkingResponse;
use App\Ussd\States\WouldYouLikeToContinueState;
use Illuminate\Http\Request;
use Sparors\Ussd\Context;
use Sparors\Ussd\ContinuingMode;
use Sparors\Ussd\Ussd;

class UssdController extends Controller
{
    public function __invoke(Request $request)
    {
        $lastText = $request->input('text') ?? '';

        if (strlen($lastText) > 0) {
            $lastText = explode('*', $lastText);
            $lastText = end($lastText);
        }

        return Ussd::build(
            Context::create(
                $request->input('sessionId'),
                $request->input('phoneNumber'),
                $lastText
            )
            ->with(['phone_number' => $request->input('phoneNumber')])
        )
        ->useInitialState(MenuAction::class)
        ->useContinuingState(ContinuingMode::CONFIRM, now()->addMinute(), WouldYouLikeToContinueState::class)
        ->useResponse(AfricasTalkingResponse::class)
        ->run();
    }
}
```

### Conditional Branching

Use USSD action to conditional decide which state should be the next.

``` php
<?php

namespace App\Http\Ussd\Actions;

use Sparors\Ussd\Contracts\Action;
use App\Http\Ussd\States\PaymentSuccessState;
use App\Http\Ussd\States\PaymentErrorState;

class MakePayment extends Action
{
    public function execute(Record $record): string
    {
        $response = Http::post('/payment', [
            'phone_number' => $record->phoneNumber
        ]);

        if ($response->ok()) {
            return PaymentSuccessState::class;
        }

        return PaymentErrorState::class;
    }
}
```

### Group logic with USSD configurator

You can use configurator to simplify repetitive parts of your application so they can be shared easily.

```php
<?php

namespace App\Http\Ussd\Configurators;

use Sparors\Ussd\Contracts\Configurator;

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

### Testing

You can easily test how your ussd application with our testing utilities

``` php
<?php

namespace App\Tests\Feature;

use Sparors\Ussd\Ussd;

final class UssdTest extends TestCase
{
    public function test_ussd_runs()
    {
        Ussd::test(WelcomeState::class)
            ->additional(['network' => 'MTN', 'phone_number' => '123123123'])
            ->actingAs('isaac')
            ->start()
            ->assertSee('Welcome...')
            ->assertContextHas('network', 'MTN')
            ->assertContextHas('phone_number')
            ->assertContextMissing('name')
            ->input('1')
            ->assertSee('Now see the magic...')
            ->assertRecordHas('choice');
    }
}
```

## Documentation

You'll find the documentation on [https://github.com/sparors/laravel-ussd/wiki](https://github.com/sparors/laravel-ussd/wiki) for V3 and [https://sparors.github.io/ussd-docs](https://sparors.github.io/ussd-docs/) for V2.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email isaacsai030@gmail.com instead of using the issue tracker.

## Credits

- [Sparors Inc][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/sparors/laravel-ussd.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/sparors/laravel-ussd.svg?style=flat-square
[ico-github]: https://img.shields.io/github/actions/workflow/status/sparors/laravel-ussd/php.yml?style=flat-square

[link-packagist]: https://packagist.org/packages/sparors/laravel-ussd
[link-downloads]: https://packagist.org/packages/sparors/laravel-ussd
[link-github]: https://github.com/sparors/laravel-ussd/actions/workflows/php.yml
[link-author]: https://github.com/sparors
[link-contributors]: ../../contributors
