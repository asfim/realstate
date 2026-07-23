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
        Schema::table('featured_projects', function (Blueprint $table) {
            $table->string('video_file')->nullable();
            $table->string('video_thumbnail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('featured_projects', function (Blueprint $table) {
            $table->dropColumn(['video_file', 'video_thumbnail']);
        });
    }
};
