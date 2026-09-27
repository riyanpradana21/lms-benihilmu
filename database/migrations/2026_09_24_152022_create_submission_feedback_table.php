<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->unique()->constrained('assignment_submissions')->cascadeOnDelete();
            $table->text('feedback');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_feedback');
    }
};
