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
        Schema::table('vendors', function (Blueprint $table): void {
            $table->string('store_name')->nullable();
            $table->string('slug')->nullable()->unique('vendors_slug_unique');
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('support_phone')->nullable();
            $table->unsignedSmallInteger('commission_rate_bps')->default(0);
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->string('approval_status')->default(MarketplaceApprovalStatusEnum::PENDING->value);
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->index(['status', 'approval_status'], 'vendors_state_idx');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table): void {
            $table->dropIndex('vendors_state_idx');
            $table->dropUnique('vendors_slug_unique');
            $table->dropColumn([
                'store_name',
                'slug',
                'description',
                'logo_path',
                'support_phone',
                'commission_rate_bps',
                'status',
                'approval_status',
                'approved_at',
                'rejection_reason',
            ]);
        });
    }
};
