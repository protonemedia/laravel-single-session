<?php

namespace Pbmedia\SingleSession\Listeners;

use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Token;

class RevokeOldTokens
{
    /**
     * Handle the event.
     *
     * @param  LaravelPassportEventsAccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        Token::where('user_id', $event->userId)
            ->where('client_id', $event->clientId)
            ->where('id', '!=', $event->tokenId)
            ->update(['revoked' => true]);
    }
}
