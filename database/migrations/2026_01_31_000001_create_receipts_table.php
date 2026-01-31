<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            // Receipt Info
            $table->string('receipt_number')->unique();
            $table->string('owner_name');
            $table->string('owner_contact')->nullable();
            $table->text('pawn_shop_name')->nullable();

            // Items in receipt (stored as JSON)
            // Example: [{"grams": 3, "karat_id": 1}, {"grams": 1, "karat_id": 5}]
            $table->json('items');

            // Simple lukat fee (single amount)
            $table->decimal('lukat_fee', 12, 2);

            // Calculated values
            $table->decimal('total_item_value', 14, 2)->nullable();
            $table->decimal('profit_margin', 14, 2)->nullable(); // Value - Lukat Fee

            // Status and final offer
            $table->enum('status', ['pending', 'offered', 'accepted', 'completed', 'rejected'])->default('pending');
            $table->decimal('final_buying_price', 14, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
