<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\Auth\RegisteredUserController;
use App\Http\Controllers\Api\Auth\SessionController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [SessionController::class, 'store'])->name('login.store');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

Route::middleware('auth:sanctum','ability:action:crud')->group(function () {
    Route::delete('/logout', [SessionController::class, 'destroy'])->name('logout');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');



    //superadmin
    //add new admin
    Route::post('/admins', [AdminController::class, 'store']);
    Route::delete('/tasks/{task}/force-delete', [TaskController::class, 'forceDelete'])->name('tasks.force-delete')->withTrashed();

    //admin
    Route::patch('/tasks/{task}/restore', [TaskController::class, 'restore'])->name('tasks.restore')->withTrashed();
    //to see all the users
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::post('/tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    Route::delete('/tasks/{task}/unassign/{user}', [TaskController::class, 'unassign'])->name('tasks.unassign');

});
