<?php

namespace Pbmedia\SingleSession\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Encryption\Encrypter;
use Pbmedia\SingleSession\Middleware\InteractsWithApiToken;

class VerifyUserSessionInApiToken
{
    use InteractsWithApiToken;

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
        return (array) JWT::decode(
            $this->encrypter->decrypt($request->cookie(static::$cookie)),
            $this->encrypter->getKey(), ['HS256']
        );
    }
}
