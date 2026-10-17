<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proverbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained();
            $table->string('slug')->unique();
            $table->text('text');
            $table->text('text_normalized');
            $table->char('text_hash', 64);
            $table->text('literal_translation')->nullable();
            $table->text('meaning')->nullable();
            $table->text('usage_example')->nullable();
            $table->text('origin_note')->nullable();
            $table->foreignId('idea_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('draft'); // draft|pending|approved|rejected|archived
            $table->string('source_type')->default('wiktionary'); // wiktionary|user|ai|public_domain
            $table->string('source_url')->nullable();
            $table->string('source_title')->nullable();
            $table->string('source_revision')->nullable();
            $table->string('license')->nullable();
            $table->text('attribution')->nullable();
            $table->unsignedTinyInteger('enrichment_score')->default(0);
            $table->decimal('ai_confidence', 3, 2)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['language_id', 'text_hash']);
            $table->fullText(['text', 'literal_translation', 'meaning']);
        });

        Schema::create('proverb_theme', function (Blueprint $table) {
            $table->foreignId('proverb_id')->constrained()->cascadeOnDelete();
            $table->foreignId('theme_id')->constrained()->cascadeOnDelete();
            $table->primary(['proverb_id', 'theme_id']);
        });

        Schema::create('country_proverb', function (Blueprint $table) {
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('proverb_id')->constrained()->cascadeOnDelete();
            $table->primary(['country_id', 'proverb_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_proverb');
        Schema::dropIfExists('proverb_theme');
        Schema::dropIfExists('proverbs');
    }
};
