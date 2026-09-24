<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\AdminStoreRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{

    // add new admin
    public function store(AdminStoreRequest $request)
    {
        $user = $request->user();
        if ($user->cannot('addAdmin', User::class)) {
            abort(403, 'You are not authorized to add an admin');
        }

        $validatedData=$request->safe()->all();

        // create admin
        $admin=User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'admin'
        ]);

        Log::channel('auth')->info('Admin created', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user'=>$admin
        ]);
    }
}
