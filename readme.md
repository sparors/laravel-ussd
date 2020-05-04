# Laravel Ussd

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

Build Ussd (Unstructured Supplementary Service Data) applications with laravel without breaking a sweat.

## Installation

You can install the package via composer:

``` bash
$ composer require sparors/laravel-ussd
```

Laravel Ussd provides zero configuration out of the box. To publish the config, run the vendor publish command:

``` bash
$ php artisan vendor:publish --provider="Sparors\Ussd\UssdServiceProvider" --tag=config
```

## Usage

### Creating States

We provide a ussd artisan command which allows you to quickly create new states.

``` bash
php artisan ussd:state Welcome
````

### Creating Nested States

Linux/Unix

``` bash
php artisan ussd:state Airtime/Welcome
````

Windows

``` bash
php artisan ussd:state Airtime\Welcome
````

Welcome state class generated

``` php
<?php

namespace App\Http\Ussd;

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

### Creating Menus

Add your menu to the beforeRendering method

``` php
<?php

namespace App\Http\Ussd;

use Sparors\Ussd\State;

class Welcome extends State
{
    protected function beforeRendering(): void
    {
       $this->menu->text('Welcome To LaravelUSSD')
            ->lineBreak(1)
            ->line('Select an option')
            ->listing(['Airtime Topup', 'Data Bundle', 'TV Subscription', 'ECG/GWCL', 'Talk To Us'])
            ->lineBreak(2);
            ->line('Powered by Sparors')
    }

    protected function afterRendering(string $argument): void
    {
        //
    }
}
```

### Creating Decisions

Add your decision to the afterRendering method

``` php
<?php

namespace App\Http\Ussd;

use App\Http\Ussd\GetRecipientNumber;
use App\Http\Ussd\MaintenanceMode;
use App\Http\Ussd\Error;
use Sparors\Ussd\State;

class Welcome extends State
{
    protected function beforeRendering(): void
    {
       $this->menu->text('Welcome To Laravel Ussd')
            ->lineBreak(1)
            ->line('Select an option')
            ->listing(['Airtime Topup', 'Data Bundle', 'TV Subscription', 'ECG/GWCL', 'Talk To Us'])
            ->lineBreak(2);
            ->line('Powered by Sparors')
    }

    protected function afterRendering(string $argument): void
    {
        // If input is equal to 1, 2, 3, 4 or 5, render the appropriate state
        $this->decision->equal('1', GetRecipientNumber::class)
                       ->equal('2', MaintenanceMode::class)
                       ->equal('3', MaintenanceMode::class)
                       ->equal('4', MaintenanceMode::class)
                       ->equal('5', MaintenanceMode::class)
                       ->any(Error::class);
    }
}
```

### Using States

Import the welcome state class and pass it to the setInitialState method

``` php
<?php

namespace App\Http\Controllers;

use Sparors\Ussd\Facades\Ussd;
use App\Http\Ussd\Welcome;

class UssdController extends Controller
{
	public function index()
	{
	    $ussd = Ussd::machine()
	        ->setInput('1')
	        ->setNetwork('MTN')
	        ->setSessionId('12350')
	        ->setPhoneNumber('0545112466')
	        ->setInitialState(Welcome::class);

	    return response()->json($ussd->run());
	}
}
```

### Running the application

You can use the development server the ships with Laravel by running, from the project root:

```bash
php artisan serve
```
You can visit [http://localhot:8000](http://localhot:8000) to see the application in action.

Enjoy!!!

### Documentation

You'll find the documentation on [https://sparors.github.io/ussd-docs](https://sparors.github.io/ussd-docs/).


### Testing

``` bash
$ composer test
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
