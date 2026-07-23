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
        Schema::create('property_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->json('gallery')->nullable();
            $table->json('floor_plans')->nullable();
            $table->text('map_url')->nullable();
            $table->string('address')->nullable();
            $table->string('land_area')->nullable();
            $table->string('no_of_floors')->nullable();
            $table->string('apartments_per_floor')->nullable();
            $table->string('launch_date')->nullable();
            $table->string('expected_completion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_details');
    }
};
