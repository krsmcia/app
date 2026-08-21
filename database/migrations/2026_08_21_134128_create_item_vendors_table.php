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
        Schema::create('item_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('vendor_sku')->nullable();
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->unsignedInteger('minimum_order_qty')->default(1);
            $table->unsignedInteger('lead_time')->nullable();
            $table->boolean('is_preferred')->default(false);
            $table->timestamps();
            $table->unique(['item_id', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_vendors');
    }
};