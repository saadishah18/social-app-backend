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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name')->nullable();
            $table->string('email')->unique();
            $table->unsignedInteger('created_by')->nullable();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('role');
            $table->string('profile_image')->nullable();
            $table->unsignedInteger('plan_id');
            $table->boolean('is_approved')->default(0);
            $table->boolean('is_active')->default(0);
            $table->rememberToken();
            $table->timestamps();

//            $table->foreign('plan_id')->references('id')->on('plans');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
