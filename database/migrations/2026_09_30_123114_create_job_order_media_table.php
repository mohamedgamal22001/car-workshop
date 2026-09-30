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
        Schema::create('job_order_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_order_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('media_type')->default('before'); // 'before', 'after'
            $table->string('file_name')->nullable();
            $table->timestamps();

            $table->index(['job_order_id', 'media_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_media');
    }
};
