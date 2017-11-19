<?php

namespace Pbmedia\SingleSession\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

class DestroyPreviousUserSession
{
    private $session;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Handle the event.
     *
     * @param  IlluminateAuthEventsLogin  $event
     * @return void
     */
    public function handle(Login $event)
    {
        if (!$previousSessionId = $event->user->session_id) {
            return;
        }

        $this->session->getHandler()->destroy($previousSessionId);

        if ($destoryEventClass = Config::get('single-session.destroy_event')) {
            Event::dispatch(new $destoryEventClass($event->user, $previousSessionId));
        }
    }
}
