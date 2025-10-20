<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Pega apenas projetos do usuário autenticado
        $projects = Project::where('user_id', auth()->id())->get();
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'nullable|in:active,completed,cancelled'
        ]);

        // Cria projeto associado ao usuário autenticado
        $project = Project::create([
            ...$validated,
            'user_id' => auth()->id()
        ]);

        return response()->json($project, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // Verifica se o projeto pertence ao usuário
        if ($project->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_FORBIDDEN);
        }

        // Carrega as tarefas relacionadas
        $project->load('tasks');
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'sometimes|in:active,completed,cancelled'
        ]);

        $project->update($validated);
        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    // DELETE /api/projects/{id} - Deletar projeto
    public function destroy(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_FORBIDDEN);
        }

        $project->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
