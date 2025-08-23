<?php

namespace LaravelBridge\LaravelBridge\Tests;

use Dedoc\Scramble\ScrambleServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use LaravelBridge\LaravelBridge\LaravelBridgeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Disable wrapping of JSON resources
        JsonResource::withoutWrapping();
    }

    protected function getPackageProviders($app)
    {
        return [
            ScrambleServiceProvider::class,
            LaravelBridgeServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        config()->set('app.env', 'local');
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        
        // Set the environment to local for Scramble access
        $app['env'] = 'local';
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }
}
