<?php

use App\Enums\ProductReviewApprovalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('body');
            $table->string('approval_status')->default(ProductReviewApprovalStatusEnum::PENDING->value);
            $table->foreignId('approved_by_admin_id')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by_admin_id')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('verified_purchase_at')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'approval_status'], 'product_reviews_item_approval_idx');
            $table->index(['user_id', 'item_id'], 'product_reviews_user_item_idx');
            $table->index(['approval_status', 'created_at'], 'product_reviews_approval_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
