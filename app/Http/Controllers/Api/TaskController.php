<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = request()->user();

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $tasks = Task::with(['category', 'users']);
        } else
            $tasks = $user->tasks();

        if ($request->hasAny(['status', 'priority'])) {

            $status = $request->input('status', null);
            $priority = $request->input('priority', null);

            if ($status)
                $tasks->where('status', $status);

            if ($priority)
                $tasks->where('priority', $priority);

        }

//        return response()->json($tasks->get());
        $tasks = $tasks->paginate(3);
        $tasks->load(['category']);
        return  $tasks->toResourceCollection();
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

        $task=$user->tasks()->create($validated);
        Log::channel('task')->info('Task created', ['task_id' => $task->id, 'user_id' => $user->id,]);
        //you  can create this way or
        /*
    $task = Task::create($validatedData);
    $user->tasks()->attach($task->id);
         */

        return $task->toResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task)
    {
        $user = $request->user();

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $task->load(['category', 'users']);
            return $task->toResource();
        }

        if ($user->can('view', $task)) {
            $task->load('category');
            return $task->toResource();
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
            Log::channel('task')->info('Task updated', ['task_id' => $task->id, 'user_id' => $request->user()->id, 'changes' => $task->getChanges(),]);
        return  $task->toResource();
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
            Log::channel('task')->info('Task deleted', ['task_id' => $task->id, 'user_id' => $user->id]);
            return response()->json(['message' => 'Task successfully deleted']);
        }
        abort(403, 'You are not authorized to delete this task');
    }

    public function restore(Request $request , Task $task)
    {
        $user=request()->user();
        if ($user->can('restore', $task)) {
            $task->restore();
            Log::channel('task')->info('Task restored', ['task_id' => $task->id, 'user_id' => $user->id,]);
            $task->load(['category']);
            return  $task->toResource();
        }
        abort(403, 'You are not authorized to restore this task');
    }

    public function forceDelete(Request $request, Task $task)
    {
        $user=request()->user();
        if ($user->can('forceDelete', $task)) {
            $task->forceDelete();
            Log::channel('task')->info('Task force deleted', ['task_id' => $task->id, 'user_id' => $user->id,]);
            return response()->json(['message' => 'Task force deleted successfully']);
        }
        abort(403, 'You are not authorized to force delete this task');
    }

    public function assign(Request $request, Task $task)
    {
        $user = $request->user();

        if ($user->can('assign', $task)) {
            $validated = $request->validate([
                'user_ids' => ['required', 'array', 'min:1'],
                //here we check evrey single id is exits in db or not
                'user_ids.*' => ['exists:users,id'],
            ]);

            $changes = $task->users()->sync($validated['user_ids']);

            Log::channel('task')->info('Task users assigned', [
                'task_id' => $task->id,
                'user_id' => $user->id,
                'attached_users' => $changes['attached'],
                'detached_users' => $changes['detached'],
            ]);

            return response()->json([
                'message' => "Task {$task->id} assigned successfully",
                'assigned_users' => $request->input('user_ids', [])
            ]);
        }

        abort(403, 'You are not authorized to assign this task');
    }

    public function unassign(Request $request, Task $task, User $user)
    {
        $user_check = $request->user();
        if ($user_check->can('unassign', $task)) {

            //here we go all users of this task and check if in user table this id exits or not
            if ($task->users()->where('users.id', $user->id)->exists())
            {
                $task->users()->detach($user->id);
                Log::channel('task')->info('User unassigned from task', [
                    'task_id' => $task->id,
                    'user_id' => $user_check->id,
                    'unassigned_user_id' => $user->id,
                ]);

                return response()->json([
                    'message' => "User {$user->id} unassigned from Task {$task->id} successfully",
                    'user_id' => $user->id,
                ]);
            }
            abort(404, "User {$user->id} is not assigned to this task");
        }
        abort(403, 'You are not authorized to unassign users from this task');
    }
}
