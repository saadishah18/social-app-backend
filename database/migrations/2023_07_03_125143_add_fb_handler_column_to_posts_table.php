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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('fb_handler')->after('post_image')->nullable();
            $table->string('insta_handler')->after('fb_handler')->nullable();
            $table->string('tiktok_handler')->after('insta_handler')->nullable();
            $table->string('sanpchat_handler')->after('tiktok_handler')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('fb_handler');
            $table->dropColumn('insta_handler');
            $table->dropColumn('tiktok_handler');
            $table->dropColumn('sanpchat_handler');
        });
    }
};
