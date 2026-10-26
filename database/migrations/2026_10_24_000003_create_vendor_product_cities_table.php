<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_product_cities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vendor_product_id')->constrained('vendor_products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('shipping_price')->default(0);
            $table->timestamps();

            $table->unique(['vendor_product_id', 'city_id'], 'vpc_offer_city_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_product_cities');
    }
};
