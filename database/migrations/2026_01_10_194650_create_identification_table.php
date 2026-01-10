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
        Schema::create('identification', function (Blueprint $table) {
            $table->id('identification_id');
            $table->unsignedBigInteger('person_id');
            $table->string('number', 13)->unique();
            $table->string('created_by', 255)->default('system');
            $table->string('modified_by', 255)->nullable();
            $table->timestamp('creation_date')->useCurrent();
            $table->timestamp('modification_date')->nullable();            
            $table->foreign('person_id')->references('person_id')->on('person')->onDelete('cascade');
            $table->index('person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identification');
    }
};
