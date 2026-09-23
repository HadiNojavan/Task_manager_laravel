<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

class UserController
{
    //to see all the user and admins
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->can('viewAny', User::class)) {
            $users=User::query();

            if ($request->has('role')) {
                $role = $request->role;
                $users = $users->where('role', $role);
            }
            return response()->json($users->get());
        }
        abort(403,'You are not authorized to view users only admins');

    }

    //to force delete user by admin
    public function destroy(Request $request, User $user)
    {

        if ($user->can('delete', User::class)) {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully.',
            'user' => $user]);
        }
        abort(403,'You are not authorized to delete users . only admins can do it ');
    }
}
