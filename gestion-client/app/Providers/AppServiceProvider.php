<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Socialite::extend('keycloak', function ($app) {
            $config = $app['config']['services']['keycloak'];
            return new \SocialiteProviders\Keycloak\Provider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect'],
                [
                    'base_url' => $config['base_url'],
                    'realms' => $config['realms'],
                ],
                $config['scopes']
            );
        });
    }
}