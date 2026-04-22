<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE cards DROP FOREIGN KEY cards_package_id_foreign');
        DB::statement('ALTER TABLE cards DROP FOREIGN KEY cards_user_id_foreign');
        DB::statement('ALTER TABLE cards DROP FOREIGN KEY cards_package_id_foreign');
        DB::statement('ALTER TABLE cards DROP FOREIGN KEY cards_user_id_foreign');

        Schema::table('cards', function (Blueprint $table) {
            $table->foreignId('package_id')
                ->nullable()
                ->change();
            $table->foreignId('user_id')
                ->nullable()
                ->change();
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        DB::statement('ALTER TABLE chats DROP FOREIGN KEY chats_admin_id_foreign');

        Schema::table('chats', function (Blueprint $table) {
            $table->foreignId('admin_id')
                ->nullable()
                ->change();
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->foreign('admin_id')
                ->references('id')
                ->on('admins')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE cards DROP FOREIGN KEY cards_package_id_foreign');
        DB::statement('ALTER TABLE cards DROP FOREIGN KEY cards_user_id_foreign');
        DB::statement('ALTER TABLE chats DROP FOREIGN KEY chats_admin_id_foreign');

        Schema::table('cards', function (Blueprint $table) {
            $table->foreignId('package_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('admins')
                ->onDelete('set null');
        });
    }
};
