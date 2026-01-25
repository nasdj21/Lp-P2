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
        Schema::create('person', function (Blueprint $table) {
            $table->id('person_id');
            $table->unsignedBigInteger('user_account_id')->unique();
            $table->string('first_name', 255);
            $table->string('last_name', 255)->nullable();
            $table->date('birthdate');
            $table->unsignedBigInteger('gender');
            $table->unsignedBigInteger('occupation');
            $table->unsignedBigInteger('marital_status');
            $table->unsignedBigInteger('education');
            $table->string('phone', 10);            
            $table->string('created_by', 255)->default('system');
            $table->string('modified_by', 255)->nullable();
            $table->timestamp('creation_date')->useCurrent();
            $table->timestamp('modification_date')->nullable();
            
            $table->foreign('user_account_id')->references('user_account_id')->on('user_account')->onDelete('cascade');
            $table->foreign('gender')->references('gender_id')->on('gender')->onDelete('cascade');
            $table->foreign('occupation')->references('occupation_id')->on('occupation')->onDelete('cascade');
            $table->foreign('marital_status')->references('marital_status_id')->on('marital_status')->onDelete('cascade');
            $table->foreign('education')->references('education_id')->on('education')->onDelete('cascade');            
            
            $table->index('user_account_id');
            $table->index('gender');
            $table->index('occupation');
            $table->index('marital_status');
            $table->index('education');            
        });

        // Constraints de validación
        DB::statement("ALTER TABLE person ADD CONSTRAINT chk_birthdate_past CHECK (birthdate <= CURRENT_DATE)");
        DB::statement("ALTER TABLE person ADD CONSTRAINT chk_phone_number CHECK (phone ~ '^\d{8,10}$')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person');
    }
};
