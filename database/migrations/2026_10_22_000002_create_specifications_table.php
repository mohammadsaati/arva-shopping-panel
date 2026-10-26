<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('specification_group_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->string('type');
            $table->string('unit')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_highlighted')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['specification_group_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specifications');
    }
};
