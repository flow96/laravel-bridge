<?php

namespace LaravelBridge\LaravelBridge\Tests\Commands;

use Illuminate\Support\Facades\Http;
use LaravelBridge\LaravelBridge\Tests\TestCase;

class GenerateClientCommandTest extends TestCase
{
    /** @test */
    public function it_can_run_generate_command_in_dry_run_mode()
    {
        Http::fake([
            'http://localhost:8000/docs/api.json' => Http::response($this->getTestSchema(), 200),
        ]);

        $this->artisan('bridge:generate', ['--dry-run' => true])
            ->expectsOutputToContain('Laravel Bridge - TypeScript Client Generator')
            ->expectsOutputToContain('DRY RUN MODE - No files will be generated')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_shows_help_information()
    {
        $this->artisan('bridge:generate', ['--help'])
            ->assertExitCode(0);
    }

    /** @test */
    public function it_displays_configuration_information()
    {
        Http::fake([
            'http://localhost:8000/docs/api.json' => Http::response($this->getTestSchema(), 200),
        ]);

        $this->artisan('bridge:generate', [
            '--dry-run' => true,
            '--client' => 'axios',
            '--output' => 'custom/output'
        ])
        ->expectsOutputToContain('Output Directory: custom/output')
        ->expectsOutputToContain('Client Type: axios')
        ->assertExitCode(0);
    }

    /** @test */
    public function it_validates_schema_before_proceeding()
    {
        Http::fake([
            'http://localhost:8000/docs/api.json' => Http::response([], 404),
        ]);

        $this->artisan('bridge:generate')
            ->expectsOutputToContain('Failed to access OpenAPI schema at: http://localhost:8000/docs/api.json')
            ->assertExitCode(1);
    }

    protected function getTestSchema(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => ['title' => 'Test API', 'version' => '1.0.0'],
            'paths' => [
                '/test' => [
                    'get' => [
                        'operationId' => 'getTest',
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['type' => 'object']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
