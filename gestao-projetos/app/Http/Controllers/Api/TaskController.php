<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())->with('project')->get();
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id'
        ]);

        // Verifica se o projeto pertence ao usuário
        if (!auth()->user()->projects()->where('id', $validated['project_id'])->exists()) {
            return response()->json(['error' => 'Projeto não encontrado'], Response::HTTP_NOT_FOUND);
        }

        $task = Task::create([
            ...$validated,
            'user_id' => auth()->id()
        ]);

        return response()->json($task, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_FORBIDDEN);
        }

        $task->load('project');
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'project_id' => 'sometimes|exists:projects,id'
        ]);

        // Se estiver atualizando o project_id, verifica se o novo projeto pertence ao usuário
        if (isset($validated['project_id']) && $validated['project_id'] !== $task->project_id) {
            if (!auth()->user()->projects()->where('id', $validated['project_id'])->exists()) {
                return response()->json(['error' => 'Projeto não encontrado'], Response::HTTP_NOT_FOUND);
            }
        }

        $task->update($validated);
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_FORBIDDEN);
        }

        $task->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
