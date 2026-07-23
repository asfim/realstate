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
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('land_area')->nullable();
            $table->string('no_of_floors')->nullable();
            $table->string('apartments_per_floor')->nullable();
            $table->string('launch_date')->nullable();
            $table->string('expected_completion')->nullable();
            $table->string('nearest_landmark')->nullable();
            $table->text('map_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('brochure')->nullable();
            $table->json('gallery')->nullable();
            $table->json('floor_plans')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('featured_projects', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'address',
                'land_area',
                'no_of_floors',
                'apartments_per_floor',
                'launch_date',
                'expected_completion',
                'nearest_landmark',
                'map_url',
                'video_url',
                'brochure',
                'gallery',
                'floor_plans',
            ]);
        });
    }
};
