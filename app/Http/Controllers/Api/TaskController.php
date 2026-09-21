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
        $user=request()->user();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending,completed,incomplete'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['required', 'date','after_or_equal:today'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        //you  can create this way or
        $task=$user->tasks()->create($validated);
        /*
    $task = Task::create($validatedData);
    $user->tasks()->attach($task->id);
         */

        return response()->json($task);
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

        if ($request->user()->can('update', $task)) {
            $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'status' => ['sometimes', 'in:pending,completed,incomplete'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'due_date' => ['sometimes', 'date','after_or_equal:today'],
            'category_id' => ['sometimes', 'exists:categories,id'],
        ]);

        $task->update($validated);

        return response()->json($task);
        }
        abort(403, 'You are not authorized to update this task');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $user=request()->user();
        if ($user->can('delete', $task)) {
            $task->delete();
            return response()->json(['message' => 'Task successfully deleted']);
        }
        abort(403, 'You are not authorized to delete this task');
    }

    public function restore(Request $request , Task $task)
    {
        $user=request()->user();
        if ($user->can('restore', $task)) {
            $task->restore();
            return response()->json($task);
        }
        abort(403, 'You are not authorized to restore this task');
    }

    public function forceDelete(Request $request, Task $task)
    {
        $user=request()->user();
        if ($user->can('forceDelete', $task)) {
            $task->forceDelete();
            return response()->json(['message' => 'Task force deleted successfully']);
        }
        abort(403, 'You are not authorized to force delete this task');
    }
}
