<?php

namespace Pbmedia\SingleSession\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Session\Session;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Cookie;

class BindSessionToFreshApiToken
{
    private $encrypter;
    private $session;
    public static $cookie = 'laravel_token_id';

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

        if ($this->shouldReceiveFreshToken($response)) {
            $response->withCookie($this->makeCookie(
                $request->user($guard)
            ));
        }

        return $response;
    }

    /**
     * Determine if the given request should receive a fresh token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return bool
     */
    protected function shouldReceiveFreshToken($response)
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === Passport::cookie()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a new API token cookie with the Session ID.
     *
     * @param  \Illuminate\Foundation\Auth\User  $user
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function makeCookie(User $user)
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
