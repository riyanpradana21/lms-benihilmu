<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('assignment_submissions')->cascadeOnDelete();
            $table->string('filename');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_attachments');
    }
};
