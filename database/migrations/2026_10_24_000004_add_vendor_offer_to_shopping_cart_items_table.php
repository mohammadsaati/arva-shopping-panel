<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shopping_cart_items', function (Blueprint $table): void {
            $table->foreignId('vendor_product_id')
                ->nullable()
                ->constrained('vendor_products')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shopping_cart_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('vendor_product_id');
        });
    }
};
