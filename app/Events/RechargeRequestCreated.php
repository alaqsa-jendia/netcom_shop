<?php

namespace App\Events;

use App\Models\RechargeRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RechargeRequestCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rechargeRequest;

    public function __construct(RechargeRequest $rechargeRequest)
    {
        $this->rechargeRequest = $rechargeRequest;
    }
}
