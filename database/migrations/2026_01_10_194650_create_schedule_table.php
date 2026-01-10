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
        Schema::create('schedule', function (Blueprint $table) {
            $table->id('schedule_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('created_by', 255)->default('system');
            $table->string('modified_by', 255)->nullable();
            $table->timestamp('creation_date')->useCurrent();
            $table->timestamp('modification_date')->nullable();
            $table->index('date');
        });

        DB::statement("ALTER TABLE schedule ADD CONSTRAINT chk_schedule_time CHECK (start_time >= TIME '07:00:00' AND end_time <= TIME '21:00:00' AND start_time < end_time)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule');
    }
};
