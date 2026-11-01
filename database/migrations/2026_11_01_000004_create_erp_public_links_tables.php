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
        Schema::create('erp_public_property_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('erp_units')->cascadeOnDelete();
            $table->string('sync_status', 32)->default('synced');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('property_id');
            $table->unique('unit_id');
        });

        Schema::create('erp_public_project_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_project_id')->constrained('featured_projects')->cascadeOnDelete();
            $table->foreignId('erp_project_id')->constrained('erp_projects')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('featured_project_id');
            $table->unique('erp_project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_public_project_links');
        Schema::dropIfExists('erp_public_property_links');
    }
};
