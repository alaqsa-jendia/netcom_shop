<?php

use App\Models\PushSubscription;

return [
    'vapid' => [
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@example.com'),
    ],

    'model' => PushSubscription::class,

    'expire' => [
        'after' => 4 * 30, // Store subscription for 4 months
    ],
];
