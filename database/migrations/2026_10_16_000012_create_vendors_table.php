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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('vendor_type')->constrained('vendor_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('company_name');
            $table->string('store_name')->nullable();
            $table->string('slug')->nullable()->unique('vendors_slug_unique');
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('support_phone')->nullable();
            $table->foreignId('commission_id')->nullable()->constrained('commissions')->cascadeOnUpdate()->nullOnDelete();
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->string('approval_status')->default(MarketplaceApprovalStatusEnum::PENDING->value);
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['status', 'approval_status'], 'vendors_state_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
