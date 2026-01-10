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
        Schema::create('appointment', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('scheduled_by');
            $table->unsignedBigInteger('worker_schedule_id')->unique();
            $table->unsignedBigInteger('status');
            $table->string('created_by', 255)->default('system');
            $table->string('modified_by', 255)->nullable();
            $table->timestamp('creation_date')->useCurrent();
            $table->timestamp('modification_date')->nullable();            
            $table->foreign('payment_id')->references('payment_id')->on('payment')->onDelete('cascade');
            $table->foreign('scheduled_by')->references('person_id')->on('person')->onDelete('cascade');
            $table->foreign('worker_schedule_id')->references('worker_schedule_id')->on('worker_schedule')->onDelete('cascade');
            $table->foreign('status')->references('status_id')->on('appointment_status')->onDelete('cascade');
            $table->index('payment_id');
            $table->index('scheduled_by');
            $table->index('worker_schedule_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment');
    }
};
