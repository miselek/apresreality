<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('zip', 10)->nullable();
            $table->decimal('gps_lat', 10, 7)->nullable();
            $table->decimal('gps_lng', 10, 7)->nullable();
            $table->string('property_type');
            $table->string('disposition')->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->decimal('land_area', 10, 2)->nullable();
            $table->decimal('price', 14, 2)->nullable();
            $table->string('price_type')->default('prodej');
            $table->decimal('commission_percent', 5, 2)->nullable();
            $table->decimal('commission_amount', 14, 2)->nullable();
            $table->decimal('ad_budget', 14, 2)->nullable();
            $table->decimal('ad_spent', 14, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('nabor');
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('price_analysis_id')->nullable()->constrained('price_analyses')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
