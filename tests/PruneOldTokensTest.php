<?php

namespace Pbmedia\SingleSession\Tests;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Laravel\Passport\PassportServiceProvider;
use Pbmedia\SingleSession\SingleSessionServiceProvider;

class PruneOldTokensTest extends TestCase
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
    public function it_prunes_the_old_token()
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

        $refreshTokens = DB::table('oauth_refresh_tokens')->get();

        $this->assertTrue((bool) $refreshTokens->get(0)->revoked);
        $this->assertFalse((bool) $refreshTokens->get(1)->revoked);
    }
}
