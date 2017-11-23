<?php

namespace Pbmedia\SingleSession\Listeners;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\Bridge\RefreshToken;
use Laravel\Passport\Events\RefreshTokenCreated;

class PruneOldTokens
{
    /**
     * Handle the event.
     *
     * @param  LaravelPassportEventsRefreshTokenCreated  $event
     * @return void
     */
    public function handle(RefreshTokenCreated $event)
    {
        DB::table('oauth_refresh_tokens')
            ->where('id', '!=', $event->refreshTokenId)
            ->where('access_token_id', '!=', $event->accessTokenId)
            ->update(['revoked' => true]);
    }
}
