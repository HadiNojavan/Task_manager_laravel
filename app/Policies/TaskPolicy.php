<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */

    //here before any excution of any method on policy first we will check this
    //the ability here means the methods of this policy
    public function before(User $user, string $ability): bool|null
    {
        if ($ability === 'forceDelete')
            return null;

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }
        return null;
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        if ($task->belongsToUser($user)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $task->belongsToUser($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $task->belongsToUser($user);
    }

    /**
     * Determine whether the user can restore the model.
     */

    //when we use can in controller laravel automatcilly pass user as first argument even if we dont include it
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->isSuperAdmin();
    }

}
