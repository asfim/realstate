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
        Schema::rename('featured_properties', 'featured_projects');
    }

    public function down(): void
    {
        Schema::rename('featured_projects', 'featured_properties');
    }
};
