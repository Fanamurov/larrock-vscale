<?php

namespace Larrock\ComponentVscale;

use Illuminate\Support\ServiceProvider;

class LarrockComponentVscaleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/../views', 'larrock');
        $this->publishes([
            __DIR__.'/../views' => base_path('resources/views/vendor/larrock'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('larrockvscale', function () {
            $class = config('larrock.components.vscale', VscaleComponent::class);

            return new $class;
        });
    }
}
