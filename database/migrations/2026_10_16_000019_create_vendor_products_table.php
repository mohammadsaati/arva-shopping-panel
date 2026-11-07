<?php

use App\Enums\GlobalStatusEnum;
use App\Enums\MarketplaceApprovalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedBigInteger('shipping_price')->default(0);
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->string('approval_status')->default(MarketplaceApprovalStatusEnum::PENDING->value);
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'vendor_id']);
            $table->index('vendor_id', 'vp_vendor_fk_idx');
            $table->index(['product_id', 'status', 'approval_status', 'price'], 'vp_catalog_idx');
            $table->index(['vendor_id', 'status', 'approval_status'], 'vp_vendor_state_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_products');
    }
};
