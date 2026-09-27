<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->integer('estimated_minutes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
