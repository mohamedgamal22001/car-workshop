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
        Schema::create('job_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, waiting_parts, ready, delivered
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->timestamp('expected_delivery_date')->nullable();
            $table->timestamps();

            $table->index(['workshop_id', 'status']);
            $table->index(['assigned_technician_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_orders');
    }
};
