<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

//to register new user
class RegisteredUserController extends Controller
{

    public function create()
    {
        //return view('auth.register');
    }

    public function store(Request $request)
    {
        dd($request->all());
        $validated=$request->validate([
        'name'=>['required','string','max:255'],
        'email'=>['required','string','email','max:255','unique:student'],
        'password'=>['required','string','min:8'],
    ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::User
        ]);

        //login user
        Auth::login($user);

        return redirect()->route('tasks.index');

    }
}
