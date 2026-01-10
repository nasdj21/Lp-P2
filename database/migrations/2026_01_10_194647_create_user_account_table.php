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
        Schema::create('user_account', function (Blueprint $table) {
            $table->id('user_account_id');
            $table->unsignedBigInteger('role_id');
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->unsignedBigInteger('status');
            $table->timestamp('last_login')->nullable();
            $table->string('created_by', 255)->default('system');
            $table->string('modified_by', 255)->nullable();
            $table->timestamp('creation_date')->useCurrent();
            $table->timestamp('modification_date')->nullable();
            
            $table->foreign('role_id')->references('role_id')->on('role')->onDelete('cascade');
            $table->foreign('status')->references('status_id')->on('user_account_status')->onDelete('cascade');
            
            $table->index('role_id');
            $table->index('status');
        });

        // Constraint de validación de email
        DB::statement("ALTER TABLE user_account ADD CONSTRAINT chk_email_format CHECK (email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_account');
    }
};
