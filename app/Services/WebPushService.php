<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    public function sendNotificationToAdmins(string $title, string $body, ?string $url = null): int
    {
        $auth = [
            'VAPID' => [
                'subject' => config('webpush.vapid.subject'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ];

        $webPush = new WebPush($auth);

        $subscriptions = PushSubscription::where('subscribable_type', Admin::class)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->get();

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => asset('images/notification-icon.png'),
            'url' => $url ?? route('admin.recharge_requests'),
            'tag' => 'recharge-notification',
            'renotify' => true,
        ]);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                $subscription->toWebPush(),
                $payload
            );
        }

        $webPush->flush();

        return $subscriptions->count();
    }
}
