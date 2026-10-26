<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specification_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('specification_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('value');
            $table->string('label');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['specification_id', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specification_options');
    }
};
