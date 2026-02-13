<?php

namespace App\Providers;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        $this->app->bind(Client::class, function ($app, array $parameters = []) {
            $token = $parameters['token'] ?? null;

            return new Client(
                config('polydock.base_url'),
                $token
            );
        });
    }
}
