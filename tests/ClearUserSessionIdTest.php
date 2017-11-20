<?php

namespace Pbmedia\SingleSession\Tests;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Pbmedia\SingleSession\SingleSessionServiceProvider;

class ClearUserSessionIdTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SingleSessionServiceProvider::class,
        ];
    }

    /** @test */
    public function it_clears_the_session_id_when_a_user_logs_in()
    {
        $user = new FakeUser;

        $user->session_id = Str::random(40);

        Event::fire(
            new Login($user, false)
        );

        $this->assertEquals(null, strlen($user->session_id));
        $this->assertTrue($user->saved());
    }
}
