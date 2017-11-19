<?php

namespace Pbmedia\SingleSession\Tests;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;
use Pbmedia\SingleSession\Listeners\StoreUserSessionId;

class StoreUserSessionIdTest extends TestCase
{
    /** @test */
    public function it_updates_the_session_id_on_the_authenticated_user()
    {
        $user = new FakeUser;

        $event = new Login($user, false);

        app(StoreUserSessionId::class)->handle($event);

        $this->assertEquals(40, strlen($user->session_id));
        $this->assertEquals($user->session_id, Session::getId());
        $this->assertTrue($user->saved());
    }
}
