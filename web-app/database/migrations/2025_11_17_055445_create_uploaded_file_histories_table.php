<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uploaded_file_histories', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('status')->default('pending');
            $table->string('stored_path');
            $table->unsignedBigInteger('file_size');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_file_histories');
    }
};
