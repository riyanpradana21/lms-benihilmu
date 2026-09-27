<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_banks')->cascadeOnDelete();
            $table->enum('type', ['multiple_choice', 'multiple_response', 'true_false', 'short_answer', 'essay']);
            $table->text('question_text');
            $table->text('explanation')->nullable();
            $table->unsignedInteger('score')->default(1);
            $table->text('correct_answer')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
