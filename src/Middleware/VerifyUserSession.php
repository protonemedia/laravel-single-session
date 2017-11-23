<?php

namespace Pbmedia\SingleSession\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;

class VerifyUserSession
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function handle($request, Closure $next)
    {
        if ($request->user()->session_id === $this->session->getId()) {
            return $next($request);
        }

        Auth::logout();

        throw new AuthenticationException;
    }
}
