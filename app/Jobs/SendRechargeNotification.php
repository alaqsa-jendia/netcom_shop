<?php

namespace App\Jobs;

use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to send Telegram notification for recharge requests
 * 
 * This runs synchronously by default (not async queue)
 * To make async: implement ShouldQueue
 */

class SendRechargeNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Make it synchronous (run immediately)
    public int $tries = 1;
    public int $timeout = 30;

    public $rechargeRequest;

    public function __construct($rechargeRequest)
    {
        $this->rechargeRequest = $rechargeRequest;
    }

    public function handle(TelegramService $telegram)
    {
        Log::info('SendRechargeNotification JOB: Starting', [
            'id' => $this->rechargeRequest->id,
        ]);

        // Reload with relations
        $recharge = \App\Models\RechargeRequest::with(['user', 'paymentMethod'])
            ->find($this->rechargeRequest->id);

        if (!$recharge) {
            Log::error('SendRechargeNotification JOB: Recharge not found', [
                'id' => $this->rechargeRequest->id,
            ]);
            return;
        }

        // Send notification
        $telegram->sendRechargeNotification($recharge);
        
        Log::info('SendRechargeNotification JOB: Completed');
    }

    public function failed(\Throwable $exception)
    {
        Log::error('SendRechargeNotification JOB: FAILED', [
            'error' => $exception->getMessage(),
        ]);
    }
}