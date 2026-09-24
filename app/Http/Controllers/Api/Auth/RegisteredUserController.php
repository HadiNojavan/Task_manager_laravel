<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

//to register new user
class RegisteredUserController extends Controller
{

    public function create()
    {
        //return view('auth.register');
    }

    public function store(RegisterUserRequest $request)
    {

        $validated=$request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::User
        ]);

        Log::channel('auth')->info('User registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role->value,
        ]);

        //login user
        //we dont need we kind of login because we dont use seesion we use api
        //Auth::login($user);

        $token=$user->createToken('authToken',['action:crud'],now()->addMinutes(10))->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user'=>$user,
            'message'=>'User Created Successfully'
        ]);

    }
}
