<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('admins', 'telegram_chat_id')) {
            Schema::table('admins', function (Blueprint $table): void {
                $table->string('telegram_chat_id', 32)->nullable()->unique('admins_telegram_chat_id_unique');
            });
        }

        if (! Schema::hasColumn('admins', 'telegram_connected_at')) {
            Schema::table('admins', function (Blueprint $table): void {
                $table->timestamp('telegram_connected_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admins', 'telegram_connected_at')) {
            Schema::table('admins', function (Blueprint $table): void {
                $table->dropColumn('telegram_connected_at');
            });
        }

        if (Schema::hasColumn('admins', 'telegram_chat_id')) {
            Schema::table('admins', function (Blueprint $table): void {
                $table->dropUnique('admins_telegram_chat_id_unique');
                $table->dropColumn('telegram_chat_id');
            });
        }
    }
};
