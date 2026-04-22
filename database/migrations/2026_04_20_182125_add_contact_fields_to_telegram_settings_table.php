<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('telegram_settings', function (Blueprint $table) {
            $table->string('contact_phone')->nullable();
            $table->string('whatsapp_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('telegram_settings', function (Blueprint $table) {
            $table->dropColumn(['contact_phone', 'whatsapp_number']);
        });
    }
};
