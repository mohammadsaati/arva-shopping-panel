<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activation_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code', 10);
            $table->timestamp('expiry_time');
            $table->boolean('is_used')->default(false);
            $table->timestamps();

            $table->index(['customer_id', 'is_used', 'expiry_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activation_codes');
    }
};
