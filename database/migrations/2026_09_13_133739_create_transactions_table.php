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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Who gave the cash
            $table->foreignId('from_user_id')->nullable()->constrained('users')->restrictOnDelete();
            // Who received the cash
            $table->foreignId('to_user_id')->nullable()->constrained('users')->restrictOnDelete();
            // Vendor involved in the transaction, if any
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->restrictOnDelete();
            $table->enum('type', [
                'purchased',    //Accounting -> vendor
                'released',     //Accounting -> Procurement
                'transfer',     //Procurement -> Procurement
                'spent',        //Actual amount of spent
                'returned',     //Procurement -> Accounting
                'adjustment',   //Only Admin available just in case
            ]);
            $table->decimal('amount', 15, 2);
            $table->text('remark')->nullable();
            // User who recorded the transaction
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
