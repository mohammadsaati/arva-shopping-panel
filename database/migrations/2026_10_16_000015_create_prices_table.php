<?php

use App\Enums\GlobalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('before')->nullable();
            $table->unsignedBigInteger('after');
            $table->unsignedBigInteger('discount')->nullable();
            $table->timestamp('off_deadline')->nullable();
            $table->string('status')->default(GlobalStatusEnum::ACTIVE->value);
            $table->timestamps();

            $table->index(['product_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
