<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user=request()->user();
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $tasks = Task::all();
        }
        else {
            $tasks = $user->tasks()->get();
        }

        return response()->json($tasks);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,Task $task)
    {
        //this $task return all task info
        // dd($task);
        if ($request->user()->can('view', $task)) {
            //there is no need for this
            //$task=Task::find($task);
            return response()->json($task);
        }
        abort(403, 'You are not authorized to view this task');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
