<?php

namespace Pbmedia\SingleSession\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

class ClearUserSessionId
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
        $user = $event->user->fresh();

        $previousSessionId = $user->session_id;

        $user->session_id = null;
        $user->save();

        if ($previousSessionId && $destroyEventClass = Config::get('single-session.destroy_event')) {
            Event::dispatch(new $destroyEventClass($event->user, $previousSessionId));
        }
    }
}
