<?php

namespace Pbmedia\SingleSession\Tests;

use Firebase\JWT\JWT;
use Laravel\Passport\Http\Middleware\CreateFreshApiToken;
use Laravel\Passport\PassportServiceProvider;
use Pbmedia\SingleSession\Middleware\BindSessionToFreshApiToken;
use Pbmedia\SingleSession\SingleSessionServiceProvider;
use Pbmedia\SingleSession\Tests\User;

class BindSessionToFreshApiTokenTest extends TestCase
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
    public function it_binds_the_session_to_the_api_token_cookie()
    {
        $this->app['router']->get('/', function () {})
            ->middleware(['web', 'auth', BindSessionToFreshApiToken::class, CreateFreshApiToken::class]);

        $user = User::create([
            'name'     => 'API User',
            'email'    => 'api@laravel.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($user)
            ->call('GET', '/', [], [
                session()->getName() => encrypt(session()->getId()),
            ])
            ->assertStatus(200)
            ->assertCookie(BindSessionToFreshApiToken::cookie());

        $sessionCookie = null;

        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === BindSessionToFreshApiToken::cookie()) {
                $sessionCookie = $cookie;
            }
        }

        $decodedValue = (array) JWT::decode(
            app('encrypter')->decrypt($sessionCookie->getValue()),
            app('encrypter')->getKey(), ['HS256']
        );

        $this->assertEquals($user->id, $decodedValue['sub']);
        $this->assertEquals(session()->getId(), $decodedValue['sessionId']);
    }
}
