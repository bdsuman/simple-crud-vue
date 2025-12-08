<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\RegisterRequest;
use App\Http\Resources\Api\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * @group Auth
 * @unauthenticated 
 */
class RegisterController extends Controller
{
    /**
     * Register
     * 
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        // Check if user already exists
        $existingUser = User::where('email', Str::lower(trim($credentials['email'])))->first();
        if ($existingUser) {
            return error_response('email_already_exists', 409);
        }

        // Create new user
        $user = User::create([
            'full_name' => $credentials['full_name'],
            'email' => Str::lower(trim($credentials['email'])),
            'password' => Hash::make($credentials['password']),
            'language' => $credentials['language'] ?? 'en',
        ]);

        // Issue a Sanctum token
        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'message' => 'registration_successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => null,
                'user' => new UserResource($user),
            ],
        ], 201);
    }
}
