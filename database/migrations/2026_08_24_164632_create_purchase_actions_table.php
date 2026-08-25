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
        Schema::create('purchase_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_workflow_item_id');
            $table->string('action', 30);
            $table->foreignId('acted_by')->constrained('users')->restrictOnDelete();
            $table->text('comment')->nullable();
            $table->timestamp('acted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_actions');
    }
};
