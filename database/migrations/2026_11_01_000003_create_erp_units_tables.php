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
        Schema::create('erp_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('project_id')->constrained('erp_projects')->restrictOnDelete();
            $table->foreignId('building_id')->nullable()->constrained('erp_buildings')->nullOnDelete();
            $table->foreignId('floor_id')->nullable()->constrained('erp_floors')->nullOnDelete();
            $table->foreignId('block_id')->nullable()->constrained('erp_blocks')->nullOnDelete();
            $table->foreignId('road_id')->nullable()->constrained('erp_roads')->nullOnDelete();

            $table->string('unit_code', 50);
            $table->string('title', 255)->nullable();
            $table->string('type', 32); // flat, plot, shop, office, commercial_other
            $table->string('status', 32)->default('available'); // draft, available, held, booked, sold, registration_pending, registered, handed_over, cancelled
            $table->string('facing', 32)->nullable(); // north, south, east, west, north_east, south_east, north_west, south_west
            
            $table->decimal('area', 18, 4);
            $table->string('area_unit', 32)->default('sqft'); // sqft, sqm, katha, decimal
            $table->decimal('asking_price', 18, 2);
            $table->decimal('min_price', 18, 2)->nullable();

            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('balconies')->nullable();
            $table->boolean('parking_included')->default(false);
            $table->decimal('road_width_ft', 8, 2)->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('lock_version')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'unit_code']);
            $table->index(['company_id', 'status']);
            $table->index(['project_id', 'type', 'status']);
        });

        Schema::create('erp_unit_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('erp_units')->cascadeOnDelete();
            $table->decimal('old_price', 18, 2);
            $table->decimal('new_price', 18, 2);
            $table->foreignId('changed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('erp_unit_status_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('erp_units')->cascadeOnDelete();
            $table->string('from_status', 32);
            $table->string('to_status', 32);
            $table->foreignId('actor_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('erp_unit_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('erp_units')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('category', 50)->default('brochure');
            $table->string('file_path', 255);
            $table->string('file_type', 50)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_unit_documents');
        Schema::dropIfExists('erp_unit_status_events');
        Schema::dropIfExists('erp_unit_price_histories');
        Schema::dropIfExists('erp_units');
    }
};
