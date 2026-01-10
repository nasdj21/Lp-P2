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
        Schema::create('professional_service', function (Blueprint $table) {
            $table->id('professional_service_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('person_id');
            $table->string('created_by', 255)->default('system');
            $table->string('modified_by', 255)->nullable();
            $table->timestamp('creation_date')->useCurrent();
            $table->timestamp('modification_date')->nullable();
            $table->foreign('service_id')->references('service_id')->on('service')->onDelete('cascade');
            $table->foreign('person_id')->references('person_id')->on('professional')->onDelete('cascade');            
            $table->index('service_id');
            $table->index('person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_service');
    }
};
