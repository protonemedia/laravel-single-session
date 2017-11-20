<?php

namespace Pbmedia\SingleSession\Middleware;

trait InteractsWithApiToken
{
    /**
     * The name for API token cookies.
     *
     * @var string
     */
    public static $cookie = 'laravel_token_id';

    public function getCookie($response, $cookieName)
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $cookieName) {
                return $cookie;
            }
        }
    }
}
