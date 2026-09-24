<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

//to login
class SessionController extends Controller
{
    public function store(Request $request)
    {
        $bearerToken = $request->bearerToken();

        if ($bearerToken) {
            $accessToken = PersonalAccessToken::findToken($bearerToken);

            if ($accessToken) {
                Log::channel('auth')->warning('Login attempted with an active token', [
                    'token_id' => $accessToken->id,
                    ]);

                return response()->json([
                    'message' => 'Please logout first',
                ], 409);
            }
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {

            Log::channel('auth')->warning('Login failed', [
                'email' => $credentials['email'],
            ]);

            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $token = $user->createToken('authToken', ['action:crud'], now()->addMinutes(10))->plainTextToken;

        Log::channel('auth')->info('User logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'message' => 'Login successful',
        ]);
    }

    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $user=$request->user();
        Log::channel('auth')->info('User logged out', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
