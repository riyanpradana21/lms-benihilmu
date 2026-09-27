<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('grade_component_id')->constrained('grade_components')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0);
            $table->foreignId('graded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'grade_component_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};
