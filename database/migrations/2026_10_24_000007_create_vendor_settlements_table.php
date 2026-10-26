<?php

use App\Enums\VendorSettlementStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_settlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('status')->default(VendorSettlementStatusEnum::PENDING->value);
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'status', 'created_at'], 'vendor_settlements_list_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_settlements');
    }
};
