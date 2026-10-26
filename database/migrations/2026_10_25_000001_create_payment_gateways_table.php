<?php

use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentCurrencyEnum;
use App\Enums\PaymentGatewayDriverEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table): void {
            $table->id();
            $table->string('driver')->unique();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('status')->default(GlobalStatusEnum::INACTIVE->value);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        DB::table('payment_gateways')->insert([
            'driver' => PaymentGatewayDriverEnum::ZARINPAL->value,
            'name' => 'زرین‌پال',
            'status' => GlobalStatusEnum::INACTIVE->value,
            'settings' => json_encode([
                'merchant_id' => '',
                'sandbox' => true,
                'currency' => PaymentCurrencyEnum::IRT->value,
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
