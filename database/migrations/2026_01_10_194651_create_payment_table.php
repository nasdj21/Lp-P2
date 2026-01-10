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
        Schema::create('payment', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('status_id');
            $table->string('file', 500)->nullable();
            $table->string('created_by', 255)->default('system');
            $table->string('modified_by', 255)->nullable();
            $table->timestamp('creation_date')->useCurrent();
            $table->timestamp('modification_date')->nullable();
            $table->foreign('person_id')->references('person_id')->on('client')->onDelete('cascade');
            $table->foreign('service_id')->references('service_id')->on('service')->onDelete('cascade');
            $table->foreign('status_id')->references('status_id')->on('payment_status')->onDelete('cascade');            
            $table->index('person_id');
            $table->index('service_id');
            $table->index('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
