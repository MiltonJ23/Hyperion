<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{


    /**
     * Get a JWT via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        Log::info('--- LOGIN ATTEMPT START ---');
        Log::info('Attempting login for email: ' . $request->input('email'));

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed.', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }




        $user = User::where('email', $request->input('email'))->first();


        if (! $user) {
            Log::error('User NOT found for email: ' . $request->input('email'));
            return response()->json(['error' => 'Unauthorized. User not found.'], 401);
        } else {
            Log::info('User found. User ID: ' . $user->user_id);
        }


        $passwordCorrect = Hash::check($request->input('password'), $user->password);
        Log::info('Password check result: ' . ($passwordCorrect ? 'true' : 'false'));

        if (! $passwordCorrect) {
            Log::error('Password check FAILED for user ID: ' . $user->user_id);
            return response()->json(['error' => 'Unauthorized. Password incorrect.'], 401);
        }


        Log::info('Credentials correct. Creating token for user ID: ' . $user->user_id);
        $token = JWTAuth::fromUser($user);

        return $this->createNewToken($token, $user);
    }

    protected function createNewToken($token, User $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth('api')->refresh());
    }

}
