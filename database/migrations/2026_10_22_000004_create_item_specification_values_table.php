<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_specification_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('specification_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('specification_option_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('value_text')->nullable();
            $table->decimal('value_number', 15, 3)->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['item_id', 'specification_id'], 'item_spec_values_item_spec_idx');
            $table->index(['specification_id', 'specification_option_id'], 'item_spec_values_spec_option_idx');
            $table->index(['specification_id', 'value_number'], 'item_spec_values_spec_number_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_specification_values');
    }
};
