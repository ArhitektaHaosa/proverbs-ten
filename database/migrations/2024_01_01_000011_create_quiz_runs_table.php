<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained();
            $table->string('quiz_type');
            $table->date('run_date');
            $table->string('seed');
            $table->json('questions');
            $table->json('answers')->nullable();
            $table->unsignedSmallInteger('score')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['player_id', 'quiz_type', 'run_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_runs');
    }
};
