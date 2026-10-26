<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_review_points', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_review_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('sentiment', 16);
            $table->string('body', 255);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_review_id', 'sentiment', 'sort_order'], 'review_points_review_sentiment_order_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_review_points');
    }
};
