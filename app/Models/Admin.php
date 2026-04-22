<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    public $timestamps = true;

    protected $fillable = [
        'username',
        'password',
        'name',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($admin) {
            foreach ($admin->chats as $chat) {
                $chat->delete();
            }
        });
    }
}
