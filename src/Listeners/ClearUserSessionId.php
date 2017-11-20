<?php

namespace Pbmedia\SingleSession\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Session\Session;

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

        $user->session_id = null;
        $user->save();
    }
}
