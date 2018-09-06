<?php

namespace Pbmedia\SingleSession\Tests;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Pbmedia\SingleSession\SingleSessionServiceProvider;

class StoreUserSessionIdTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SingleSessionServiceProvider::class,
        ];
    }

    /** @test */
    public function it_stores_the_session_id_on_the_authenticated_user()
    {
        $user = new FakeUser;

        Event::fire(
            new Authenticated(Auth::guard(), $user)
        );

        $this->assertEquals(40, strlen($user->session_id));
        $this->assertEquals($user->session_id, Session::getId());
        $this->assertTrue($user->saved());
    }
}
