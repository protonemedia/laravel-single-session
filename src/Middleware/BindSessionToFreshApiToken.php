<?php

namespace Pbmedia\SingleSession\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Cookie;

class BindSessionToFreshApiToken
{
    private $encrypter;
    private $session;
    public static $cookie = 'laravel_token_id';

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
     * Get or set the name for token cookies.
     *
     * @param  string|null  $cookie
     * @return string
     */
    public static function cookie($cookie = null)
    {
        if (is_null($cookie)) {
            return static::$cookie;
        }

        static::$cookie = $cookie;

        return new $cookie;
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
        $response = $next($request);

        if ($request->user($guard)) {
            $response->withCookie($this->makeCookie(
                $request->user($guard)
            ));
        }

        return $response;
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
            static::cookie(),
            $token,
            $expiration,
            $config['path'],
            $config['domain'],
            $config['secure'],
            true
        );
    }
}
