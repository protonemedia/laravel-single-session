<?php

namespace Pbmedia\SingleSession;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Events\RefreshTokenCreated;
use Pbmedia\SingleSession\Listeners;

class SingleSessionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/single-session.php' => config_path('single-session.php'),
        ], 'config');

        if (!class_exists('AddSessionIdToUsersTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/add_session_id_to_users_table.php.stub' =>
                database_path('migrations/' . date('Y_m_d_His', time()) . '_add_session_id_to_users_table.php'),
            ], 'migrations');
        }

        foreach ([Listeners\DestroyPreviousUserSession::class, Listeners\StoreUserSessionId::class] as $listener) {
            Event::listen(Login::class, $listener);
        }

        if (Config::get('single-session.prune_and_revoke_tokens')) {
            Event::listen(AccessTokenCreated::class, Listeners\RevokeOldTokens::class);
            Event::listen(RefreshTokenCreated::class, Listeners\PruneOldTokens::class);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/single-session.php', 'single-session');
    }
}
