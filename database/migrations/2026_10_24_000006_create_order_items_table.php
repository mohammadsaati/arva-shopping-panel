<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('vendor_order_id')
                ->nullable()
                ->constrained('vendor_orders')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('vendor_product_id')
                ->nullable()
                ->constrained('vendor_products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->unsignedInteger('count');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('off_price')->nullable();
            $table->unsignedBigInteger('total_price');
            $table->unsignedSmallInteger('commission_rate_bps')->default(0);
            $table->unsignedBigInteger('commission_amount')->default(0);
            $table->unsignedBigInteger('payable_amount')->default(0);
            $table->timestamps();

            $table->index(['order_id', 'product_id']);
            $table->index(['vendor_order_id', 'product_id'], 'order_items_vendor_product_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
