<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController;

class RegisterController extends BaseController
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        return User::register($request);
    }

    /**
     * Login a user.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        return User::login($request);
    }

    /**
     * Retrieve the author posts with the given user ID.
     *
     * @param int $id The ID of the user.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the author information.
     */
    public function author(Request $request, $id): JsonResponse
    {
        return User::author($request, $id);
    }

    /**
     * Retrieve a user by their ID.
     *
     * @param int $id The ID of the user to retrieve.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the user data.
     */
    public function user($id): JsonResponse
    {
        return User::user($id);
    }
}
