<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)->unique(); // ISO 639
            $table->string('name');
            $table->string('native_name')->nullable();
            $table->string('script')->nullable();
            $table->string('direction', 3)->default('ltr');
            $table->boolean('enabled')->default(true);
            $table->string('wiktionary_category')->nullable();
            $table->unsignedInteger('import_rank')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
