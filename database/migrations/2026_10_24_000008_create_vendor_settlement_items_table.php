<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_settlement_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vendor_settlement_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('vendor_order_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->timestamps();

            $table->index('vendor_order_id', 'vendor_settlement_items_order_idx');
            $table->index('vendor_settlement_id', 'vendor_settlement_items_settlement_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_settlement_items');
    }
};
