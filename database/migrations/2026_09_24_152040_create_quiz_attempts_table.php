<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('expires_at');
            $table->dateTime('submitted_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_auto_submitted')->default(false);
            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');
            $table->timestamps();

            $table->index(['quiz_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
