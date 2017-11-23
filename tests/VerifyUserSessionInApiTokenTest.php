<?php

namespace Pbmedia\SingleSession\Tests;

use Illuminate\Support\Str;
use Laravel\Passport\Http\Middleware\CreateFreshApiToken;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use Pbmedia\SingleSession\Middleware\BindSessionToFreshApiToken;
use Pbmedia\SingleSession\Middleware\VerifyUserSessionInApiToken;
use Pbmedia\SingleSession\SingleSessionServiceProvider;
use Pbmedia\SingleSession\Tests\User;

class VerifyUserSessionInApiTokenTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpPassport();
    }

    protected function getPackageProviders($app)
    {
        return [
            PassportServiceProvider::class,
            SingleSessionServiceProvider::class,
        ];
    }

    /** @test */
    public function it_verifies_the_session_id_from_the_cookie()
    {
        $this->app['router']->get('/', function () {})
            ->middleware(['web', 'auth', BindSessionToFreshApiToken::class, CreateFreshApiToken::class]);

        $this->app['router']->get('/api', function () {})
            ->middleware(['api', 'auth:api', VerifyUserSessionInApiToken::class]);

        $user = User::create([
            'name'     => 'API User',
            'email'    => 'api@laravel.com',
            'password' => bcrypt('secret'),
        ]);

        // bind the session
        $response = $this->actingAs($user)
            ->call('GET', '/', [], [
                session()->getName() => encrypt(session()->getId()),
            ]);

        $passportCookie = null;
        $sessionCookie  = null;

        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === BindSessionToFreshApiToken::cookie()) {
                $sessionCookie = $cookie;
            } else if ($cookie->getName() === Passport::cookie()) {
                $passportCookie = $cookie;
            }
        }

        // bind the session
        $this->call('GET', '/api', [], [
            Passport::cookie()                   => $passportCookie->getValue(),
            BindSessionToFreshApiToken::cookie() => $sessionCookie->getValue(),
        ], [], $this->transformHeadersToServerVars([
            'X-CSRF-TOKEN'     => session()->token(),
            'X-Requested-With' => 'XMLHttpRequest',
        ]))->assertStatus(200);
    }

    /** @test */
    public function it_returns_a_403_response_when_the_session_id_is_not_valid()
    {
        $this->app['router']->get('/', function () {})
            ->middleware(['web', 'auth', BindSessionToFreshApiToken::class, CreateFreshApiToken::class]);

        $this->app['router']->get('/api', function () {})
            ->middleware(['api', 'auth:api', VerifyUserSessionInApiToken::class]);

        $user = User::create([
            'name'     => 'API User',
            'email'    => 'api@laravel.com',
            'password' => bcrypt('secret'),
        ]);

        // bind the session
        $response = $this->actingAs($user)
            ->call('GET', '/', [], [
                session()->getName() => encrypt(session()->getId()),
            ]);

        $passportCookie = null;
        $sessionCookie  = null;

        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === BindSessionToFreshApiToken::cookie()) {
                $sessionCookie = $cookie;
            } else if ($cookie->getName() === Passport::cookie()) {
                $passportCookie = $cookie;
            }
        }

        $user->update(['session_id' => Str::random(40)]);

        // bind the session
        $this->call('GET', '/api', [], [
            Passport::cookie()                   => $passportCookie->getValue(),
            BindSessionToFreshApiToken::cookie() => $sessionCookie->getValue(),
        ], [], $this->transformHeadersToServerVars([
            'X-CSRF-TOKEN'     => session()->token(),
            'X-Requested-With' => 'XMLHttpRequest',
        ]))->assertStatus(403);
    }
}
