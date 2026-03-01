<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address');
            $table->decimal('area', 10, 2);
            $table->string('property_type');
            $table->string('condition');
            $table->integer('floor')->nullable();
            $table->string('ownership')->nullable();
            $table->decimal('estimated_price', 14, 2)->nullable();
            $table->json('comparables')->nullable();
            $table->string('report_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_analyses');
    }
};
