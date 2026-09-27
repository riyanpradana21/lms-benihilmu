<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->string('event_type');
            $table->dateTime('occurred_at')->useCurrent();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('attempt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_violations');
    }
};
