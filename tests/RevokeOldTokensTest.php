<?php

namespace Pbmedia\SingleSession\Tests;

use Laravel\Passport\Client;
use Laravel\Passport\PassportServiceProvider;
use Laravel\Passport\Token;
use Pbmedia\SingleSession\SingleSessionServiceProvider;

class RevokeOldTokensTest extends TestCase
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
    public function it_revokes_the_old_token()
    {
        User::create([
            'name'     => 'API User',
            'email'    => 'api@laravel.com',
            'password' => bcrypt('secret'),
        ]);

        $client = Client::first();

        // create two tokens
        $this->postJson('oauth/token', $data = [
            'grant_type'    => 'password',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'username'      => 'api@laravel.com',
            'password'      => 'secret',
            'scope'         => '',
        ]);

        $this->postJson('oauth/token', $data);

        $tokens = Token::all();

        $this->assertTrue($tokens->get(0)->revoked);
        $this->assertFalse($tokens->get(1)->revoked);
    }
}
