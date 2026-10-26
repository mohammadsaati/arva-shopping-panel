<?php

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('address_id')->constrained('customer_addresses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('discount_id')
                ->nullable()
                ->constrained('discounts')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('discount_code', 50)->nullable();
            $table->unsignedBigInteger('off_amount')->default(0);
            $table->unsignedBigInteger('shipping_amount')->default(0);
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('status')->default(OrderStatusEnum::PENDING->value);
            $table->string('payment_status')->default(PaymentStatusEnum::PENDING->value);
            $table->timestamp('paid_at')->nullable();
            $table->boolean('inventory_restored')->default(false);
            $table->string('tracking_code')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->text('full_address')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['payment_status', 'created_at']);
            $table->index(['discount_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
