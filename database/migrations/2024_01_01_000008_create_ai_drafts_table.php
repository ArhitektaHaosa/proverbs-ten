<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proverb_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('model');
            $table->string('prompt_version');
            $table->char('input_hash', 64);
            $table->json('output')->nullable();
            $table->string('status')->default('queued'); // queued|ok|invalid|failed|applied
            $table->text('error')->nullable();
            $table->unsignedInteger('tokens')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_drafts');
    }
};
