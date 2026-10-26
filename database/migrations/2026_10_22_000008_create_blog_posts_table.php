<?php

use App\Enums\BlogPostStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blog_category_id')->constrained('blog_categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default(BlogPostStatusEnum::DRAFT->value);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('featured_order')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['blog_category_id', 'status', 'published_at'], 'blog_posts_category_status_published_idx');
            $table->index(['status', 'published_at'], 'blog_posts_status_published_idx');
            $table->index(['is_featured', 'featured_order'], 'blog_posts_featured_order_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
