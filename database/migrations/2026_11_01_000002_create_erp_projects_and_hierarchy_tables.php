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
        Schema::create('erp_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('erp_branches')->nullOnDelete();
            $table->string('code', 50);
            $table->string('name', 255);
            $table->string('type', 32); // flat, plot, commercial, mixed
            $table->string('status', 32)->default('planning'); // planning, upcoming, ongoing, ready, completed, on_hold
            $table->text('address')->nullable();
            $table->string('location', 255)->nullable();
            $table->string('land_area', 100)->nullable();
            $table->string('land_area_unit', 32)->default('katha'); // katha, decimal, bigha, sqft, sqm
            $table->string('ownership_type', 32)->default('own_land'); // own_land, joint_venture, developer_share
            $table->date('launch_date')->nullable();
            $table->date('target_end_date')->nullable();
            $table->decimal('budget', 18, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('featured_image', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'type', 'status']);
        });

        Schema::create('erp_buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('project_id')->constrained('erp_projects')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name', 100);
            $table->unsignedInteger('total_floors')->default(0);
            $table->unsignedInteger('total_units')->default(0);
            $table->string('status', 32)->default('under_construction');
            $table->timestamps();

            $table->unique(['project_id', 'code']);
        });

        Schema::create('erp_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('project_id')->constrained('erp_projects')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'code']);
        });

        Schema::create('erp_roads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('block_id')->constrained('erp_blocks')->cascadeOnDelete();
            $table->string('road_name_or_no', 100);
            $table->decimal('width_ft', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('erp_floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('building_id')->constrained('erp_buildings')->cascadeOnDelete();
            $table->integer('floor_number');
            $table->string('floor_name', 100);
            $table->timestamps();

            $table->unique(['building_id', 'floor_number']);
        });

        Schema::create('erp_project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('project_id')->constrained('erp_projects')->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->date('target_date')->nullable();
            $table->date('completed_at')->nullable();
            $table->string('status', 32)->default('pending'); // pending, in_progress, completed
            $table->decimal('weightage_percent', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_project_milestones');
        Schema::dropIfExists('erp_floors');
        Schema::dropIfExists('erp_roads');
        Schema::dropIfExists('erp_blocks');
        Schema::dropIfExists('erp_buildings');
        Schema::dropIfExists('erp_projects');
    }
};

