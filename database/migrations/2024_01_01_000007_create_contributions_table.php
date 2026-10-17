<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proverb_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // new_proverb|translation|meaning|equivalent|usage|origin|remix
            $table->json('payload');
            $table->foreignId('player_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('ip_hash', 64)->nullable();
            $table->boolean('license_ack')->default(false);
            $table->timestamp('moderated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
