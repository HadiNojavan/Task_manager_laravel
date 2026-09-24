<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController
{
    //to see all the user and admins
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->can('viewAny', User::class)) {
//            $users=User::query();
                $users=User::with('tasks');
            if ($request->has('role')) {
                $role = $request->role;
                $users = $users->where('role', $role);
            }
            //instead of get we use paginate
            return $users->paginate(2)->toResourceCollection();
        }
        abort(403,'You are not authorized to view users only admins');

    }

    //to force delete user by admin
    public function destroy(Request $request, User $user)
    {

        if ($request->user()->can('delete', User::class)) {
            $user->delete();

            Log::info('User deleted', [
                'deleted_user_id' => $user->id,
                'deleted_user_email' => $user->email,
                'deleted_by' => $request->user()->id,
            ]);

            return response()->json(['message' => 'User deleted successfully.',
            'user' => $user]);
        }
        abort(403,'You are not authorized to delete users . only admins can do it ');
    }
}
