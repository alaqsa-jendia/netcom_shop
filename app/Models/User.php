<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'balance',
        'status',
        'referred_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function rechargeRequests()
    {
        return $this->hasMany(RechargeRequest::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            foreach ($user->cards as $card) {
                $card->delete();
            }
            foreach ($user->rechargeRequests as $request) {
                $request->delete();
            }
            foreach ($user->chats as $chat) {
                $chat->delete();
            }
            foreach ($user->notifications as $notification) {
                $notification->delete();
            }
        });
    }
}
