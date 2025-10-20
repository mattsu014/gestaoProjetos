<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Relatório: Tarefas por projeto
    public function tasksByProject()
    {
        $projects = Project::where('user_id', auth()->id())
            ->withCount(['tasks as total_tasks',
                        'tasks as completed_tasks' => function($query) {
                            $query->where('status', 'completed');
                        }])
            ->get();

        return response()->json($projects);
    }

    // Relatório: Tarefas pendentes
    public function pendingTasks(Request $request)
    {
        $query = Task::where('user_id', auth()->id())
                    ->where('status', '!=', 'completed')
                    ->with('project');

        // Filtro por projeto
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->get();

        return response()->json($tasks);
    }

    // Relatório geral
    public function general()
    {
        $user_id = auth()->id();

        $data = [
            'total_projects' => Project::where('user_id', $user_id)->count(),
            'active_projects' => Project::where('user_id', $user_id)->where('status', 'active')->count(),
            'total_tasks' => Task::where('user_id', $user_id)->count(),
            'completed_tasks' => Task::where('user_id', $user_id)->where('status', 'completed')->count(),
            'pending_tasks' => Task::where('user_id', $user_id)->where('status', 'pending')->count(),
        ];

        return response()->json($data);
    }
}
