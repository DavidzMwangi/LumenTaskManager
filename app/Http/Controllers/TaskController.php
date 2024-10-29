<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function createTask(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:tasks',
            'due_date' => 'required|date|after:today',
            'description' => 'optional',
        ]);

        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    public function getAllTasks(Request $request)
    {
        $query = Task::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        $tasks = $query->paginate(10);
        return response()->json($tasks);
    }

    public function getTask($id)
    {
        $task = Task::findOrFail($id);
        return response()->json($task);
    }

    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $this->validate($request, [
            'title' => 'sometimes|required|unique:tasks,title,' . $id,
            'due_date' => 'sometimes|date|after:today',
        ]);

        $task->update($request->all());
        return response()->json($task);
    }

    public function deleteTask($id)
    {
        Task::findOrFail($id)->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }

    //
}
