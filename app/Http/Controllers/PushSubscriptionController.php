<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;

class PushSubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'public_key' => 'nullable|string',
            'auth_token' => 'nullable|string',
            'content_encoding' => 'nullable|string',
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = PushSubscription::updateOrCreate(
            [
                'endpoint' => $request->endpoint,
            ],
            [
                'subscribable_type' => get_class($user),
                'subscribable_id' => $user->id,
                'public_key' => $request->public_key,
                'auth_token' => $request->auth_token,
                'content_encoding' => $request->content_encoding,
            ]
        );

        return response()->json(['success' => true, 'subscription_id' => $subscription->id]);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        PushSubscription::where('subscribable_type', get_class($user))
            ->where('subscribable_id', $user->id)
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'url' => 'nullable|url',
        ]);

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
            'title' => $request->title,
            'body' => $request->body,
            'icon' => asset('images/notification-icon.png'),
            'url' => $request->url ?? config('app.url'),
        ]);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                $subscription->toWebPush(),
                $payload
            );
        }

        $webPush->flush();

        return response()->json(['success' => true, 'sent_count' => $subscriptions->count()]);
    }
}
