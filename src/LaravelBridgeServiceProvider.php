<?php

namespace LaravelBridge;

use Illuminate\Support\ServiceProvider;
use LaravelBridge\Commands\GenerateClientCommand;
use LaravelBridge\Commands\Install;

class LaravelBridgeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/bridge.php',
            'bridge'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            //__DIR__.'/../config/bridge.php' => config_path('bridge.php'),
            __DIR__ . '/../vendor/dedoc/scramble/config/scramble.php' => config_path('scramble.php'),
            __DIR__.'/../config/openapi-ts.config.ts' => resource_path('js/bridge-openapi-ts.config.ts'),
        ], 'bridge-config');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateClientCommand::class,
                Install::class,
            ]);
        }
    }
}
