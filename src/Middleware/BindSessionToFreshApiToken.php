<?php

namespace Pbmedia\SingleSession\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Pbmedia\SingleSession\Middleware\InteractsWithApiToken;
use Symfony\Component\HttpFoundation\Cookie;

class BindSessionToFreshApiToken
{
    use InteractsWithApiToken;

    private $encrypter;
    private $guard;
    private $session;

    /**
     * Create the middleware.
     *
     * @return void
     */
    public function __construct(Encrypter $encrypter, Session $session)
    {
        $this->encrypter = $encrypter;
        $this->session   = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        return $next($request)->withCookie($this->makeCookie(
            $request->user($guard)
        ));
    }

    /**
     * Create a new API token cookie with the Session ID.
     *
     * @param  \Illuminate\Foundation\Auth\User  $user
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function makeCookie($user)
    {
        $config = Config::get('session');

        $expiration = Carbon::now()->addMinutes($config['lifetime']);

        $token = JWT::encode([
            'sub'       => $user->getKey(),
            'sessionId' => $this->session->getId(),
            'expiry'    => $expiration->getTimestamp(),
        ], $this->encrypter->getKey());

        return new Cookie(
            static::$cookie,
            $token,
            $expiration,
            $config['path'],
            $config['domain'],
            $config['secure'],
            true
        );
    }
}
