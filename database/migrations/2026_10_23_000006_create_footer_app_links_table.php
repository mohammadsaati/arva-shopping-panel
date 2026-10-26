<?php

use App\Enums\GlobalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_app_links', function (Blueprint $table): void {
            $table->id();
            $table->string('label');
            $table->string('image_path')->nullable();
            $table->string('url', 2048);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->timestamps();

            $table->index(['status', 'sort_order'], 'footer_app_links_status_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_app_links');
    }
};
