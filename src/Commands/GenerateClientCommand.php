<?php

namespace LaravelBridge\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class GenerateClientCommand extends Command
{
    protected $signature = 'bridge:generate
                            {--input= : Input schema file location}
                            {--output= : Output directory for generated client}
                            {--client=axios : HTTP client to use (fetch, xhr, node, axios)}';
    protected $description = 'Generate a TypeScript client from an OpenAPI schema';

    public function handle()
    {

        // Validate that openapi-ts is installed
        if (! file_exists(base_path('node_modules/@hey-api/openapi-ts'))) {
            $this->warn('@hey-api/openapi-ts not found, installing...');
            $result = Artisan::call('install:bridge');
            if ($result !== 0) {
                $this->error('Failed to install @hey-api/openapi-ts');
                return self::FAILURE;
            }
        }

        $input = $this->option('input') ?? config('bridge.client.input');
        $output = $this->option('output') ?? config('bridge.client.output');

        // ENV Variablen für den Prozess setzen
        $env = array_merge($_ENV, [
            'OPENAPI_INPUT'  => $input,
            'OPENAPI_OUTPUT' => $output,
        ]);

        $resourcePath = resource_path('js/bridge-openapi-ts.config.ts');
        $fallbackPath = realpath(__DIR__ . '/../../config/openapi-ts.config.ts');

        $configPath = File::exists($resourcePath) ? $resourcePath : $fallbackPath;
        $this->info("Using config at: " . $configPath);

        $process = new Process(['npx', '@hey-api/openapi-ts', '-f', $configPath], base_path(), $env, null, null, $env);
        $process->setTimeout(null);

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        $this->info("Client generated at: " . $output);

        if (!$process->isSuccessful()) {
            $this->error('API Client generation failed');
            return self::FAILURE;
        }

        $this->info('API Client generated successfully!');
        return self::SUCCESS;
    }
}
