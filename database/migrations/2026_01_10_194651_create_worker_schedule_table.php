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
        Schema::create('worker_schedule', function (Blueprint $table) {
            $table->id('worker_schedule_id');
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('person_id');
            $table->boolean('is_available');
            $table->string('created_by', 255)->default('system');
            $table->string('modified_by', 255)->nullable();
            $table->timestamp('creation_date')->useCurrent();
            $table->timestamp('modification_date')->nullable();            
            $table->foreign('schedule_id')->references('schedule_id')->on('schedule')->onDelete('cascade');
            $table->foreign('person_id')->references('person_id')->on('person')->onDelete('cascade');            
            $table->unique(['schedule_id', 'person_id'], 'uniq_schedule_person');
            $table->index('schedule_id');
            $table->index('person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_schedule');
    }
};
