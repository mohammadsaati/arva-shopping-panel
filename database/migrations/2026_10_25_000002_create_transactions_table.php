<?php

use App\Enums\PaymentCurrencyEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\TransactionReasonTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('reason_type')->comment(TransactionReasonTypeEnum::class);
            $table->unsignedBigInteger('reason_id')->nullable();
            $table->foreignId('payment_gateway_id')
                ->nullable()
                ->constrained('payment_gateways')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 8)->default(PaymentCurrencyEnum::IRT->value);
            $table->string('status')->default(PaymentStatusEnum::PENDING->value);
            $table->string('authority')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('card_pan')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['reason_type', 'reason_id', 'status']);
            $table->unique('authority');
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
