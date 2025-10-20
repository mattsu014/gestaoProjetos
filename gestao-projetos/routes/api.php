<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

// Rotas públicas (sem autenticação)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas (com autenticação)
Route::middleware(['auth:sanctum'])->group(function () {
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Projetos
    Route::apiResource('projects', ProjectController::class);

    // Tarefas
    Route::apiResource('tasks', TaskController::class);

    // Relatórios
    Route::prefix('reports')->group(function () {
        Route::get('/tasks-by-project', [ReportController::class, 'tasksByProject']);
        Route::get('/pending-tasks', [ReportController::class, 'pendingTasks']);
        Route::get('/general', [ReportController::class, 'general']);
    });
});
