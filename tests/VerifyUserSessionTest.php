<?php

namespace Pbmedia\SingleSession\Tests;

use Illuminate\Support\Str;
use Pbmedia\SingleSession\Middleware\VerifyUserSession;
use Pbmedia\SingleSession\SingleSessionServiceProvider;
use Pbmedia\SingleSession\Tests\User;

class VerifyUserSessionTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SingleSessionServiceProvider::class,
        ];
    }

    /** @test */
    public function it_throws_an_unauthorized_exception_if_the_session_id_is_incorrect()
    {
        $this->app['router']->get('/', function () {})
            ->middleware(['web', VerifyUserSession::class]);

        $this->app['router']->get('/login', function () {})
            ->middleware(['web'])->name('login');

        $user = User::create([
            'name'       => 'Web User',
            'email'      => 'web@laravel.com',
            'password'   => bcrypt('secret'),
            'session_id' => Str::random(40),
        ]);

        $this->actingAs($user)
            ->call('GET', '/', [], [
                session()->getName() => encrypt(session()->getId()),
            ])
            ->assertRedirect('login');
    }

    /** @test */
    public function it_verifies_the_session_id()
    {
        $this->app['router']->get('/', function () {})
            ->middleware(['web', VerifyUserSession::class]);

        $user = User::create([
            'name'     => 'Web User',
            'email'    => 'web@laravel.com',
            'password' => bcrypt('secret'),
        ]);

        $this->actingAs($user)
            ->call('GET', '/', [], [
                session()->getName() => encrypt(session()->getId()),
            ])
            ->assertStatus(200);
    }
}
