<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            if (! Schema::hasColumn('chats', 'user_one_id')) {
                $table->foreignId('user_one_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('chats', 'user_two_id')) {
                $table->foreignId('user_two_id')
                    ->nullable()
                    ->after('user_one_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('chats', 'last_message_at')) {
                $table->timestamp('last_message_at')->nullable()->after('activo');
            }
        });

        Schema::table('mensajes', function (Blueprint $table) {
            if (! Schema::hasColumn('mensajes', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('editado');
            }
        });

        DB::table('chats')->orderBy('id')->get()->each(function ($chat) {
            if ($chat->user_one_id && $chat->user_two_id) {
                return;
            }

            $firstMessage = DB::table('mensajes')
                ->where('chat_id', $chat->id)
                ->orderBy('created_at')
                ->first();

            $lastMessageAt = DB::table('mensajes')
                ->where('chat_id', $chat->id)
                ->max('created_at');

            DB::table('chats')
                ->where('id', $chat->id)
                ->update([
                    'user_one_id' => $firstMessage->emisor_id ?? $chat->user_id,
                    'user_two_id' => $firstMessage->receptor_id ?? null,
                    'last_message_at' => $lastMessageAt ?: ($chat->updated_at ?? now()),
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('mensajes', function (Blueprint $table) {
            if (Schema::hasColumn('mensajes', 'read_at')) {
                $table->dropColumn('read_at');
            }
        });

        Schema::table('chats', function (Blueprint $table) {
            if (Schema::hasColumn('chats', 'user_one_id')) {
                $table->dropConstrainedForeignId('user_one_id');
            }

            if (Schema::hasColumn('chats', 'user_two_id')) {
                $table->dropConstrainedForeignId('user_two_id');
            }

            if (Schema::hasColumn('chats', 'last_message_at')) {
                $table->dropColumn('last_message_at');
            }
        });
    }
};
