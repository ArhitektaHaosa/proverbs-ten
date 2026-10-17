<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proverb_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proverb_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_proverb_id')->constrained('proverbs')->cascadeOnDelete();
            $table->string('type'); // equivalent|close|opposite
            $table->string('status')->default('pending');
            $table->string('source')->default('ai'); // ai|user|import
            $table->timestamps();

            $table->unique(['proverb_id', 'related_proverb_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proverb_links');
    }
};
