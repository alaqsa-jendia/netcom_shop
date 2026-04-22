<?php

namespace App\Notifications;

use App\Models\RechargeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Minishlink\WebPush\Subscription;

class RechargeRequestNotification extends Notification
{
    use Queueable;

    protected RechargeRequest $rechargeRequest;

    public function __construct(RechargeRequest $rechargeRequest)
    {
        $this->rechargeRequest = $rechargeRequest;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toWebPush($notifiable, ?Subscription $subscription = null): array
    {
        $userName = $this->rechargeRequest->user?->name ?? 'مستخدم';
        $amount = number_format($this->rechargeRequest->amount, 2);

        return [
            'title' => 'طلب شحن جديد',
            'body' => "المستخدم: {$userName}\nالمبلغ: {$amount} شيكل",
            'icon' => asset('images/notification-icon.png'),
            'url' => route('admin.recharge_requests'),
        ];
    }

    public function toArray($notifiable): array
    {
        $userName = $this->rechargeRequest->user?->name ?? 'مستخدم';
        $amount = number_format($this->rechargeRequest->amount, 2);

        return [
            'title' => 'طلب شحن جديد',
            'body' => "المستخدم: {$userName}\nالمبلغ: {$amount} شيكل",
            'url' => route('admin.recharge_requests'),
            'recharge_id' => $this->rechargeRequest->id,
        ];
    }
}
