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
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, waiting_parts, ready, delivered, cancelled
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->decimal('labor_cost', 10, 2)->default(0.00); // Single source of truth for labor cost
            $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expected_delivery_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['workshop_id', 'status']);
            $table->index(['assigned_technician_id', 'status']);
            $table->index(['workshop_id', 'expected_delivery_date']);
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
