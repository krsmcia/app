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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained()->cascadeOnDelete();
            /*
            |--------------------------------------------------------------------------
            | Requested Item
            |--------------------------------------------------------------------------
            */
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            /*
            |--------------------------------------------------------------------------
            | Purchasing Result
            |--------------------------------------------------------------------------
            */
            $table->foreignId('item_vendor_id')->nullable()->constrained()->nullOnDelete();
            /*
            |--------------------------------------------------------------------------
            | Purchase Snapshot
            |--------------------------------------------------------------------------
            */
            $table->string('item_name');
            $table->string('sku', 50);
            $table->string('vendor_name')->nullable();
            $table->string('vendor_sku')->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('remark', 500)->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
