<?php

namespace Pbmedia\SingleSession\Tests;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Mockery;
use Pbmedia\SingleSession\Listeners\DestroyPreviousUserSession;
use Pbmedia\SingleSession\Tests\FakeUser;
use SessionHandlerInterface;

class DestroyPreviousUserSessionTest extends TestCase
{
    /** @test */
    public function it_destroys_no_session_if_no_previous_session_id_has_been_persisted()
    {
        $session = Mockery::mock(Session::class);
        $session->shouldNotReceive('getHandler');

        $user = new FakeUser;

        $user->session_id = null;

        $event = new Login($user, false);

        (new DestroyPreviousUserSession($session))->handle($event);
    }

    /** @test */
    public function it_destoys_the_persisted_session()
    {
        $user = new FakeUser;

        $user->session_id = Str::random(40);

        $sessionHandler = Mockery::mock(SessionHandlerInterface::class);
        $sessionHandler->shouldReceive('destroy')->with($user->session_id);

        $session = Mockery::mock(Session::class);
        $session->shouldReceive('getHandler')->andReturn($sessionHandler);

        $event = new Login($user, false);

        (new DestroyPreviousUserSession($session))->handle($event);
    }

    /** @test */
    public function it_can_dispatch_an_event_after_destroying_the_session()
    {
        $this->app['config']->set('single-session.destroy_event', FakeDestroyEvent::class);

        $user = new FakeUser;

        $user->session_id = Str::random(40);

        $event = new Login($user, false);

        Event::fake();

        app(DestroyPreviousUserSession::class)->handle($event);

        Event::assertDispatched(FakeDestroyEvent::class, function ($event) use ($user) {
            return $event->user === $user && $event->sessionId = $user->session_id;
        });
    }
}
