# Laravel USSD

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

Build USSD (Unstructured Supplementary Service Data) applications with laravel without breaking a sweat.

## Installation

You can install the package via composer:

``` bash
$ composer require sparors/laravel-ussd
```

Laravel USSD provides zero configuration out of the box. To publish the config, run the vendor publish command:

``` bash
$ php artisan vendor:publish --provider="Sparors\Ussd\UssdServiceProvider" --tag=config
```

## Usage

### Creating States

We provide an artisan ussd command which allows you to quickly create new states.

``` bash
php artisan ussd:state Welcome
````

Step 1: Welcome state class generated:

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

Step 2: Add your menu to the beforeRendering method

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

### Using states

Step 3: Import the welcome state class and pass it to the setInitialState method

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

you can use the development server the ships with Laravel by running, from the project root:

```bash
php artisan serve
```
You can visit [http://localhot:8000](http://localhot:8000) to see the application in action.

Enjoy!!!

### Running the tests

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
