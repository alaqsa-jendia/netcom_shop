<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramSettings extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'bot_token',
        'chat_id',
        'notifications_enabled',
        'contact_phone',
        'whatsapp_number',
        'system_name',
        'system_name_ar',
        'logo',
        'logo2',
    ];

    protected function casts(): array
    {
        return [
            'notifications_enabled' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
