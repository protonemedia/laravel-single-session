<?php

namespace Pbmedia\SingleSession;

use Illuminate\Support\ServiceProvider;

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
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/single-session.php', 'single-session');
    }
}
