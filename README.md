# [WIP] Laravel Single Session

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pbmedia/laravel-single-session.svg?style=flat-square)](https://packagist.org/packages/pbmedia/laravel-single-session)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/pascalbaljetmedia/laravel-single-session/master.svg?style=flat-square)](https://travis-ci.org/pascalbaljetmedia/laravel-single-session)
[![Quality Score](https://img.shields.io/scrutinizer/g/pascalbaljetmedia/laravel-single-session.svg?style=flat-square)](https://scrutinizer-ci.com/g/pascalbaljetmedia/laravel-single-session)
[![Total Downloads](https://img.shields.io/packagist/dt/pbmedia/laravel-single-session.svg?style=flat-square)](https://packagist.org/packages/pbmedia/laravel-single-session)

This package prevents a User from being logged in more than once. It destroys the previous session when User logs in.

## Requirements
* Laravel 5.5 only, PHP 7.0, 7.1 and 7.2 supported.
* Support for [Package Discovery](https://laravel.com/docs/5.5/packages#package-discovery).

## Installation

You can install the package via composer:

``` bash
composer require pbmedia/laravel-single-session
```

Publish the database migration and config file using the Artisan CLI tool.

``` bash
php artisan vendor:publish --provider="Pbmedia\SingleSession\SingleSessionServiceProvider"
```

The database migrations adds a ```session_id``` field to the ```users``` table. Run the migration to get started!

``` bash
php artisan migrate
```

## Usage

Since Laravel 5.5 has support for Package Discovery, you don't have to add the Service Provider to your ```app.php``` config file.

It assumes you use Laravel's [Authentication Quickstart](https://laravel.com/docs/5.5/authentication#authentication-quickstart) which fires an ```Illuminate\Auth\Events\Login``` event once a User is successfully logged in. If you use another authentication mechanism, make sure this event gets fired at the right moment.

In the ```single-session.php``` config file you can specify a ```destroy_event```. This event will get fired once a previous session gets destroyed. You might want to use this to [broadcast](https://laravel.com/docs/5.5/broadcasting) the event and handle the destroyed session in the user interface. The constructor of the event can take two parameters, The User model and ID of the destroyed session. Here is an example event:

```php
<?php

namespace Pbmedia\SingleSession\Tests;

class UserSessionWasDestroyed
{
    public $user;
    public $sessionId;

    public function __construct($user, $sessionId)
    {
        $this->user = $user;
        $this->sessionId = $sessionId;
    }

    public function broadcastOn()
    {
        // return new PrivateChannel('channel-name');
    }

    public function broadcastWith()
    {
        return ['user_id' => $this->user->id];
    }
}
```



## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email pascal@pascalbaljetmedia.com instead of using the issue tracker.

## Credits

- [Pascal Baljet](https://github.com/pascalbaljet)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
