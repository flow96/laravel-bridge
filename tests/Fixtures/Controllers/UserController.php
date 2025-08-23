<?php

namespace LaravelBridge\LaravelBridge\Tests\Fixtures\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelBridge\LaravelBridge\Tests\Fixtures\Models\User;
use LaravelBridge\LaravelBridge\Tests\Fixtures\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Get paginated list of users
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::all();
        return UserResource::collection($users)->response();
    }


    public function store(Request $request)
    {
        $user = User::create($request->all());
        return response(['user' => UserResource::make($user)], 201);
    }
}
