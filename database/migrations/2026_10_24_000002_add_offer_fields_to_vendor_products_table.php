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
        Schema::table('vendor_products', function (Blueprint $table): void {
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedBigInteger('shipping_price')->default(0);
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->string('approval_status')->default(MarketplaceApprovalStatusEnum::PENDING->value);
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->index(['product_id', 'status', 'approval_status', 'price'], 'vp_catalog_idx');
            $table->index(['vendor_id', 'status', 'approval_status'], 'vp_vendor_state_idx');
        });
    }

    public function down(): void
    {
        Schema::table('vendor_products', function (Blueprint $table): void {
            $table->dropIndex('vp_catalog_idx');
            $table->dropIndex('vp_vendor_state_idx');
            $table->dropColumn([
                'price',
                'stock',
                'shipping_price',
                'status',
                'approval_status',
                'approved_at',
                'rejection_reason',
            ]);
        });
    }
};
