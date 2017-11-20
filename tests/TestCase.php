<?php

namespace Pbmedia\SingleSession\Tests;

use Illuminate\Support\Str;
use Laravel\Passport\Passport;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function setUpDatabase()
    {
        $this->app['config']->set('app.key', 'base64:yWa/ByhLC/GUvfToOuaPD7zDwB64qkc/QkaQOrT5IpE=');
        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        include_once __DIR__ . '/create_users_table.php.stub';
        include_once __DIR__ . '/../database/migrations/add_session_id_to_users_table.php.stub';

        (new \CreateUsersTable)->up();
        User::unguard();

        $this->app['config']->set('auth.providers.users.model', User::class);
    }

    protected function setUpPassport()
    {
        $this->app['config']->set('auth.providers.users.model', User::class);
        $migrationsDirectory = __DIR__ . '/../vendor/laravel/passport/database/migrations/';

        foreach (scandir($migrationsDirectory) as $migration) {
            if (!Str::endsWith($migration, '.php')) {
                continue;
            }

            include_once $migrationsDirectory . $migration;
        }

        (new \CreateOauthAuthCodesTable)->up();
        (new \CreateOauthAccessTokensTable)->up();
        (new \CreateOauthRefreshTokensTable)->up();
        (new \CreateOauthClientsTable)->up();
        (new \CreateOauthPersonalAccessClientsTable)->up();

        $this->artisan('passport:keys');
        $this->artisan('passport:client', ['--password' => true, '--name' => 'Test Client']);

        Passport::routes();
    }
}
