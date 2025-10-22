<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(
            \Laravel\Jetstream\Contracts\CreatesTeams::class,
            \App\Actions\Jetstream\CreateTeam::class
        );
        $this->app->bind(
            \Laravel\Jetstream\Contracts\DeletesTeams::class,
            \App\Actions\Jetstream\DeleteTeam::class
        );
        // Add other Jetstream actions as needed
    }
}
