<?php

namespace Pbmedia\SingleSession\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Session\Session;

class StoreUserSessionId
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
        $event->user->session_id = $this->session->getId();
        $event->user->save();
    }
}
