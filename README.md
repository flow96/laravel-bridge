# Laravel Bridge

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-bridge/laravel-bridge.svg?style=flat-square)](https://packagist.org/packages/laravel-bridge/laravel-bridge)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-bridge/laravel-bridge.svg?style=flat-square)](https://packagist.org/packages/laravel-bridge/laravel-bridge)

Laravel Bridge is a powerful package that seamlessly connects your Laravel API with TypeScript clients by automatically generating type-safe client code from your OpenAPI schemas. Built on top of Laravel Scramble, it provides an effortless way to maintain synchronization between your backend API and frontend client code.

Laravel Bridge uses unde the hood:
* [Laravel Scramble](https://scramble.dedoc.co/) for OpenAPI schema generation
* [@hey-api/openapi-ts](https://github.com/hey-api/openapi-ts) for OpenAPI schema generation

For detailed configuration options see [Laravel Scramble](https://scramble.dedoc.co/) and [@hey-api/openapi-ts](https://github.com/hey-api/openapi-ts).

## Features

- 🚀 **Automatic TypeScript Client Generation**: Generate fully typed TypeScript clients from your Laravel API
- 📡 **Real-time Watching**: Monitor schema changes and regenerate clients automatically
- 🔧 **Highly Configurable**: Customize output directories, client types, and generation options
- 🎯 **Laravel Scramble Integration**: Leverages the power of Laravel Scramble for OpenAPI schema generation
- 💼 **Multiple Client Types**: Support for fetch, axios, xhr, and node HTTP clients
- 🛡️ **Type Safety**: Full TypeScript type definitions for your API endpoints
- ⚡ **Zero Configuration**: Works out of the box with sensible defaults

## Installation

### Prerequisites

Before using Laravel Bridge, ensure you have:

1. **Node.js** installed
2. **Composer** installed
3. **Laravel Api Routes** installed (`php artisan install:api`)

### Install via Composer

You can install the package via Composer:

```bash
composer require flow96/laravel-bridge
```


### Publishing the configuration

Laravel Bridge uses [Scramble](https://scramble.dedoc.co/) under the hood to generate the OpenAPI schema.
By publishing the configuration you can modify the scramble configuration in the `config/scramble.php` file.

1. Publish the configuration:
```bash
php artisan vendor:publish --tag=bridge-config
```

## Usage

### Basic Commands

Scramble (the openapi schema generator) works by default only if your app is running in the `local` environment.

#### Run your application

Your app must be running with the `local` environment in order for scramble to be able to generate the OpenAPI schema.

```bash
php artisan serve
```

#### Generate TypeScript Client

Generate a TypeScript client from your OpenAPI schema:

```bash
php artisan bridge:generate
```

### Using the Generated Client

After running the generate command, you'll find TypeScript files in your configured output directory. Here's how to use them:

#### With Axios client (default)

```typescript
import { UserService } from './client/sdk.gen';

// Create a new user
const result = await UserService.create({
    body: {
        name: "John Doe",
        email: "asd@asd.com",
        password: "superSecret"
    }
})


// Get all users
const users = await UserService.getAll();
console.log(users.data.users);
```

## Best Practices

### 1. API Documentation

For the best results, ensure your Laravel controllers are well-documented:

```php
public function index(Request $request): JsonResponse
{
    $users = User::all();

    // Type hint is necessary for Eloquent models
    /** @var App\Models\User[] */
    return response(users);
}

public function index(Request $request): JsonResponse
{
    $users = User::all();
    // Resources work out of the box
    return response(UserResource::collection($users));
}

// Parameters of the form request are automatically transformed into typescript types
public function store(CreateUserRequest $request): JsonResponse
{
    $user = User::create($request->validated());
    return response(UserResource::make($user), 201);
}


public function findByName(Request $request): JsonResponse
{
    // Query parameters are automatically transformed into typescript types
    $name = $request->query('name');
    $user = User::where('name', $name)->first();
    return response(UserResource::make($user));
}
```

### 2. Resource Classes

Use Eloquent API Resources for consistent response formatting:

```php
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```


## Credits

- [Laravel Scramble](https://scramble.dedoc.co/) for OpenAPI generation
- [OpenAPI TypeScript](https://github.com/ferdikoomen/openapi-typescript-codegen) for client generation

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.
