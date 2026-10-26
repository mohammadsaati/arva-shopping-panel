<?php

use App\Enums\GlobalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('price');
            $table->foreignId('color_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedBigInteger('shipping_price')->default(0);
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->timestamps();

            $table->index(['item_id', 'status']);
            $table->index(['color_id', 'size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
