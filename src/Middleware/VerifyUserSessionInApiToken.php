<?php

namespace Pbmedia\SingleSession\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Encryption\Encrypter;

class VerifyUserSessionInApiToken
{
    private $encrypter;

    /**
     * Create the middleware.
     *
     * @return void
     */
    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
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
        try {
            if ($request->user()->session_id === $this->decodeJwtTokenCookie($request)['sessionId']) {
                return $next($request);
            }
        } catch (Exception $e) {}

        return abort(403);
    }

    /**
     * Decode and decrypt the JWT token cookie.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function decodeJwtTokenCookie($request)
    {
        $cookie = $request->cookie(BindSessionToFreshApiToken::$cookie);

        return (array) JWT::decode(
            $this->encrypter->decrypt($cookie),
            $this->encrypter->getKey(), ['HS256']
        );
    }
}
