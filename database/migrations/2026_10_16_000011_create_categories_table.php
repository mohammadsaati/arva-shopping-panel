<?php

use App\Enums\GlobalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('categories')->cascadeOnUpdate()->nullOnDelete();
            $table->string('slug')->unique();
            $table->unsignedInteger('priority')->default(0);
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->timestamps();

            $table->index(['parent_id', 'status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
