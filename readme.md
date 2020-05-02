# Ussd

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

Create ussd with ease. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require sparors/laravel-ussd
```

Ussd is meant to provide zero configuration out of the box. But optional you can publish the configuration to customize it to suit you.

``` bash
$ php artisan vendor:publish --provider="Sparors\Ussd\UssdServiceProvider" --tag=config
```

## Usage

Create your ussd states by running the command

``` bash
php artisan ussd:state Welcome
````

After creating your states, you can link them to one another and just create a machine to run it.

``` php
<?php

use App\Ussd\Welcome;
use Illuminate\Support\Facades\Route;
use Sparors\Ussd\Facades\Ussd;

Route::get('/', function () {
    $ussd = Ussd::machine()
        ->setInput('1')
        ->setNetwork('MTN')
        ->setSessionId('12350')
        ->setPhoneNumber('0545112466')
        ->setInitialState(Welcome::class);

    return response()->json($ussd->run());
});
```

That all the magic you need to make it run

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

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
[ico-travis]: https://img.shields.io/travis/sparors/laravel-ussd/master.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/sparors/laravel-ussd
[link-downloads]: https://packagist.org/packages/sparors/laravel-ussd
[link-travis]: https://travis-ci.org/sparors/laravel-ussd
[link-author]: https://github.com/sparors
[link-contributors]: ../../contributors
