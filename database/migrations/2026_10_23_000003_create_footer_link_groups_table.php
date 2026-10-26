<?php

use App\Enums\GlobalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_link_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->timestamps();

            $table->index(['status', 'sort_order'], 'footer_link_groups_status_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_link_groups');
    }
};
