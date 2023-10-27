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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('user_name');
            $table->string('last_name')->after('first_name');
            $table->string('country_code')->after('last_name');
            $table->string('country_name')->after('country_code');
            $table->string('phone')->after('country_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('country_code');
            $table->dropColumn('country_name');
            $table->dropColumn('phone');
        });
    }
};
