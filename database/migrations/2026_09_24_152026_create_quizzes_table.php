<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->unsignedTinyInteger('attempt_limit')->default(1);
            $table->unsignedInteger('passing_score')->default(70);
            $table->boolean('is_randomized')->default(false);
            $table->unsignedInteger('violation_threshold')->default(5);
            $table->boolean('auto_submit_on_violation')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
