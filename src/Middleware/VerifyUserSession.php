<?php

namespace Pbmedia\SingleSession\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

class VerifyUserSession
{
    private $session;

    /**
     * Create the middleware.
     *
     * @return void
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function handle($request, Closure $next)
    {
        if ($request->user()->session_id === $this->session->getId()) {
            return $next($request);
        }

        if ($destroyEventClass = Config::get('single-session.destroy_event')) {
            Event::dispatch(new $destroyEventClass($event->user, $previousSessionId));
        }

        Auth::logout();

        throw new AuthenticationException;
    }
}
