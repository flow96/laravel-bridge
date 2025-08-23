<?php

namespace LaravelBridge\LaravelBridge\Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use LaravelBridge\LaravelBridge\Tests\Fixtures\Controllers\UserController;
use LaravelBridge\LaravelBridge\Tests\Fixtures\Models\User;
use LaravelBridge\LaravelBridge\Tests\TestCase;

class LaravelBridgeTest extends TestCase
{
	private string $testOutputDir = "";

	protected function setUp(): void
	{
		parent::setUp();

		$this->setupTestRoutes();

		// Create test output directory
		$this->testOutputDir = base_path('tests/output/client');
		File::ensureDirectoryExists($this->testOutputDir);
	}

	protected function setupTestRoutes(): void
	{
		Route::prefix('api')->group(function () {
			// User routes
			Route::get('users', [UserController::class, 'index']);
			Route::post('users', [UserController::class, 'store']);
		});
	}


	/** @test */
	public function it_can_create_user()
	{
		$response = $this->postJson('/api/users', [
			'name' => 'John Doe',
			'email' => 'john@example.com'
		]);

		$response->assertCreated();

		$json = $response->json();
		$this->assertIsArray($json);
		$this->assertArrayHasKey('user', $json);

		$user = $json['user'];
		$this->assertEquals('John Doe', $user['name']);
		$this->assertEquals('john@example.com', $user['email']);
	}

	/** @test */
	public function it_can_get_users()
	{
		$response = $this->getJson('/api/users');

		$response->assertOk();

		$json = $response->json();
		$this->assertIsArray($json);
		$this->assertCount(0, $json);
	}


	/** @test */
	public function it_can_load_the_openapi_schema()
	{
		$response = $this->getJson('/docs/api.json');

		$response->assertOk();

		$json = $response->json();
		$this->assertIsArray($json);
		$this->assertArrayHasKey('openapi', $json);
		$this->assertArrayHasKey('info', $json);
		$this->assertArrayHasKey('paths', $json);
	}


	/** @test */
	public function it_can_generate_client_from_local_file()
	{
		// Get the schema as JSON response
		$schemaResponse = $this->getJson('/docs/api.json');
		$schemaResponse->assertOk();
		
		// Create the schema file path and ensure directory exists
		$schemaFilePath = base_path('tests/output/client/schema.json');
		File::ensureDirectoryExists(dirname($schemaFilePath));
		
		// Write the JSON content to file
		File::put($schemaFilePath, $schemaResponse->getContent());

		// Test with absolute path
		$this->artisan('bridge:generate', [
			'--output' => 'tests/output/client',
			'--schema-url' => $schemaFilePath,
			'--dry-run' => true
		])
			->expectsOutputToContain('Laravel Bridge - TypeScript Client Generator')
			->expectsOutputToContain('DRY RUN MODE')
			->assertExitCode(0);
	}

	/** @test */
	public function it_can_generate_client_from_relative_file()
	{
		// Get the schema as JSON response
		$schemaResponse = $this->getJson('/docs/api.json');
		$schemaResponse->assertOk();
		
		// Create the schema file path and ensure directory exists
		$schemaFilePath = base_path('tests/output/client/schema.json');
		File::ensureDirectoryExists(dirname($schemaFilePath));
		
		// Write the JSON content to file
		File::put($schemaFilePath, $schemaResponse->getContent());

		// Test with relative path
		$this->artisan('bridge:generate', [
			'--output' => 'tests/output/client',
			'--schema-url' => 'tests/output/client/schema.json',
			'--dry-run' => false
		])
			->expectsOutputToContain('Laravel Bridge - TypeScript Client Generator');
			dump($schemaFilePath);
			exit();
	}



	/** @test */
	public function it_can_actually_generate_client_files()
	{

		// Get the schema as JSON response
		$schemaResponse = $this->getJson('/docs/api.json');
		$schemaResponse->assertOk();
		
		// Create the schema file path and ensure directory exists
		$schemaFilePath = base_path('tests/output/client/schema.json');
		File::ensureDirectoryExists(dirname($schemaFilePath));
		
		// Write the JSON content to file
		File::put($schemaFilePath, $schemaResponse->getContent());

		// Run the generate command WITHOUT dry-run
		$this->artisan('bridge:generate', [
			'--output' => 'tests/output/client',
			'--schema-url' => $schemaFilePath,
			'--dry-run' => false
		])
			->expectsOutputToContain('Laravel Bridge - TypeScript Client Generator')
			->expectsOutputToContain('✅ TypeScript client generated successfully!')
			->assertExitCode(0);

		// Check that files were actually generated
		$this->assertFileExists(base_path('tests/output/client/index.ts'));
		
		// Clean up
		File::deleteDirectory(base_path('tests/output'));
	}


}