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
        Schema::create('purchase_workflow_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_workflow_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_item_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('pending');
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_workflow_items');
    }
};
