<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); // ID automático
            $table->string('title'); // Título do projeto
            $table->text('description')->nullable(); // Descrição (pode ser vazia)
            $table->date('start_date'); // Data de início
            $table->date('end_date')->nullable(); // Data de término (opcional)
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active'); // Status
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relação com usuário
            $table->timestamps(); // Cria created_at e updated_at automaticamente
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
