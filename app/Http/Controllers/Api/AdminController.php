<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    // add new admin
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->cannot('addAdmin', User::class)) {
            abort(403, 'You are not authorized to add an admin');
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
        ]);

        // create admin
        $admin=User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'admin'
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user'=>$admin
        ]);
    }
}
