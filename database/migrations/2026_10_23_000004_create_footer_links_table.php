<?php

use App\Enums\GlobalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('footer_link_group_id')->constrained('footer_link_groups')->cascadeOnDelete();
            $table->string('label');
            $table->string('url', 2048);
            $table->boolean('opens_in_new_tab')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->timestamps();

            $table->index(['footer_link_group_id', 'status', 'sort_order'], 'footer_links_group_status_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_links');
    }
};
