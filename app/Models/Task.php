<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at','role'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function belongsToUser(User $user): bool
    {
        return $this->users()
            ->wherePivot('user_id', $user->id)
            ->exists();
    }

}
