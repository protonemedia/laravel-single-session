# Laravel Single Session

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pbmedia/laravel-single-session.svg?style=flat-square)](https://packagist.org/packages/pbmedia/laravel-single-session)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/pascalbaljetmedia/laravel-single-session/master.svg?style=flat-square)](https://travis-ci.org/pascalbaljetmedia/laravel-single-session)
[![Quality Score](https://img.shields.io/scrutinizer/g/pascalbaljetmedia/laravel-single-session.svg?style=flat-square)](https://scrutinizer-ci.com/g/pascalbaljetmedia/laravel-single-session)
[![Total Downloads](https://img.shields.io/packagist/dt/pbmedia/laravel-single-session.svg?style=flat-square)](https://packagist.org/packages/pbmedia/laravel-single-session)

This package prevents a User from being logged in more than once. It destroys the previous session when a User logs in and thereby allowing only one session per user. It assumes you use Laravel's [Authentication](https://laravel.com/docs/5.6/authentication) features.

## Requirements
* Laravel 5.6 only, 7.1 and 7.2 supported.
* Support for [Package Discovery](https://laravel.com/docs/5.6/packages#package-discovery).
* Support for [Laravel Passport](https://laravel.com/docs/5.6/passport).

## Notes
* Laravel 5.6.14 and later supports [invalidating sessions out-of-the-box](https://laravel.com/docs/5.6/authentication#invalidating-sessions-on-other-devices).
* If you're still using Laravel 5.5, please use version 1.2.0. 

## Installation

You can install the package via composer:

``` bash
composer require pbmedia/laravel-single-session
```

Publish the database migration and config file using the Artisan CLI tool.

``` bash
php artisan vendor:publish --provider="Pbmedia\SingleSession\SingleSessionServiceProvider"
```

The database migration adds a ```session_id``` field to the ```users``` table. Run the migration to get started!

``` bash
php artisan migrate
```

Now add the ```\Pbmedia\SingleSession\Middleware\VerifyUserSession``` middleware to the routes you want to protect.

## Usage

Since Laravel 5.5 has support for Package Discovery, you don't have to add the Service Provider to your ```app.php``` config file.

In the ```single-session.php``` config file you can specify a ```destroy_event```. This event will get fired once a previous session gets destroyed. You might want to use this to [broadcast](https://laravel.com/docs/5.6/broadcasting) the event and handle the destroyed session in the user interface. The constructor of the event can take two parameters, The User model and ID of the destroyed session. Here is an example event:

```php
<?php

namespace App\Events;

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

When using Laravel Passport it automatically prunes and revokes tokens from the database as well. This can be disabled by setting the ```prune_and_revoke_tokens``` option to ```false``` in the config file.

If you're using Laravel Passport's ```CreateFreshApiToken``` middleware, add the ```Pbmedia\SingleSession\Middleware\BindSessionToFreshApiToken``` middleware *before* the ```CreateFreshApiToken``` and add the ```VerifyUserSessionInApiToken``` middleware to the ```auth:api``` group:

```php
$router->get('/', 'HomeController@show')->middleware([
    'web', 'auth', BindSessionToFreshApiToken::class, CreateFreshApiToken::class
]);

$router->get('/api', 'ApiController@index')->middleware([
    'api', 'auth:api', VerifyUserSessionInApiToken::class
]);
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
