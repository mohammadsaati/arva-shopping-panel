<?php

use App\Enums\GlobalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_banners', function (Blueprint $table): void {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image');
            $table->string('mobile_image')->nullable();
            $table->string('target_url')->nullable();
            $table->string('target_label')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->timestamps();

            $table->index(['status', 'sort_order'], 'blog_banners_status_sort_order_idx');
            $table->index(['starts_at', 'ends_at'], 'blog_banners_schedule_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_banners');
    }
};
