<?php

namespace App\Http\Controllers;

use App\Models\RechargeRequest;
use App\Models\TelegramSettings;
use App\Models\Notification;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Telegram Webhook Controller
 * 
 * Handles callback queries from Telegram inline buttons
 * for approving/rejecting recharge requests
 */

class TelegramCallbackController extends Controller
{
    protected ?string $adminChatId = null;
    protected bool $isAdmin = false;

    public function webhook(Request $request)
    {
        Log::info('Telegram webhook called', $request->all());

        // Get settings
        $settings = TelegramSettings::first();
        if (!$settings || !$settings->notifications_enabled) {
            return response()->json(['ok' => false, 'error' => 'Notifications disabled']);
        }

        $this->adminChatId = $settings->chat_id;

        // Get callback query from Telegram
        $callbackQuery = $request->input('callback_query');
        
        if (!$callbackQuery) {
            return response()->json(['ok' => true, 'no action' => 'No callback query']);
        }

        $data = $callbackQuery['data'] ?? '';
        $chatId = $callbackQuery['message']['chat']['id'] ?? null;
        $messageId = $callbackQuery['message']['message_id'] ?? null;
        $callbackId = $callbackQuery['id'] ?? null;

        // Verify admin (only admin chat ID can approve/reject)
        if ($chatId != $this->adminChatId) {
            $this->answerCallback($callbackId, 'غير مصرح لك بهذا الإجراء');
            return response()->json(['ok' => false, 'error' => 'Unauthorized']);
        }

        // Handle callback data
        if (str_starts_with($data, 'approve_')) {
            $id = (int) str_replace('approve_', '', $data);
            return $this->handleApprove($id, $callbackId, $messageId);
        } elseif (str_starts_with($data, 'reject_')) {
            $id = (int) str_replace('reject_', '', $data);
            return $this->handleReject($id, $callbackId, $messageId);
        }

        return response()->json(['ok' => true, 'no action' => 'Unknown command']);
    }

    /**
     * Handle inline approve button
     */
    protected function handleApprove(int $id, string $callbackId, ?int $messageId): Response
    {
        Log::info('Telegram approve called', ['id' => $id]);

        $recharge = RechargeRequest::with(['user'])->find($id);

        if (!$recharge) {
            $this->answerCallback($callbackId, 'الطلب غير موجود');
            return response()->json(['ok' => false, 'error' => 'Request not found']);
        }

        if ($recharge->status !== 'pending') {
            $this->answerCallback($callbackId, 'الطلب bereits تم 처리');
            return response()->json(['ok' => false, 'error' => 'Already processed']);
        }

        // Process approval
        try {
            DB::beginTransaction();

            $user = $recharge->user;
            
            // Add balance to user
            $user->balance += $recharge->amount;
            $user->save();

            // Update recharge status
            $recharge->status = 'approved';
            $recharge->approved_at = now();
            $recharge->save();

            // Create notification for user
            Notification::create([
                'user_id' => $user->id,
                'type' => 'recharge_approved',
                'title' => 'تم قبول طلب الشحن',
                'message' => 'تم إضافة ' . number_format($recharge->amount, 2) . ' شيكل إلى رصيدك',
                'data' => [
                    'amount' => $recharge->amount,
                    'recharge_id' => $recharge->id,
                ],
            ]);

            DB::commit();

            // Answer callback
            $this->answerCallback($callbackId, '✅ تم الموافقة وإضافة الرصيد');

            // Send confirmation to group
            $telegram = new TelegramService();
            $telegram->sendMessage("✅ تم قبول طلب الشحن\n\n👤 المستخدم: {$user->name}\n💰 المبلغ: {$recharge->amount} شيكل\n\n✅ تمت الإضافة للرصيد");

            Log::info('Telegram approve completed', ['id' => $id]);

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Telegram approve failed', ['error' => $e->getMessage()]);
            $this->answerCallback($callbackId, 'حدث خطأ في العملية');
            return response()->json(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Handle inline reject button - ask for reason
     */
    protected function handleReject(int $id, string $callbackId, ?int $messageId): Response
    {
        Log::info('Telegram reject called', ['id' => $id]);

        $recharge = RechargeRequest::with(['user'])->find($id);

        if (!$recharge) {
            $this->answerCallback($callbackId, 'الطلب غير موجود');
            return response()->json(['ok' => false, 'error' => 'Request not found']);
        }

        if ($recharge->status !== 'pending') {
            $this->answerCallback($callbackId, 'الطلب bereits تم 处理');
            return response()->json(['ok' => false, 'error' => 'Already processed']);
        }

        // Ask for rejection reason
        $user = $recharge->user;
        $telegram = new TelegramService();
        
        $text = "❌ رفض طلب الشحن\n\n";
        $text .= "━━━━━━━━━━━━━━━\n";
        $text .= "👤 المستخدم: {$user->name}\n";
        $text .= "💰 المبلغ: {$recharge->amount} شيكل\n";
        $text .= "━━━━━━━━━━━━━━━\n";
        $text .= "📝 لإتمام الرفض، يرجى إرسال سبب الرفض\n";
        $text .= "استخدم الأمر: /reject_{$recharge->id} [السبب]";

        $telegram->sendMessage($text);

        $this->answerCallback($callbackId, 'أرسل سبب الرفض');

        Log::info('Telegram reject reason requested', ['id' => $id]);

        return response()->json(['ok' => true, 'waiting_for_reason' => true]);
    }

    /**
     * Handle /reject command with reason
     */
    public function rejectWithReason(Request $request)
    {
        Log::info('Telegram reject with reason called', $request->all());

        $settings = TelegramSettings::first();
        if (!$settings) {
            return response()->json(['ok' => false]);
        }

        $this->adminChatId = $settings->chat_id;

        // Get message from Telegram
        $message = $request->input('message');
        if (!$message) {
            return response()->json(['ok' => false]);
        }

        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';

        // Verify admin
        if ($chatId != $this->adminChatId) {
            return response()->json(['ok' => false, 'error' => 'Unauthorized']);
        }

        // Parse command: /reject_123 reason text
        if (!preg_match('/^\/reject_(\d+)\s+(.+)$/', $text, $matches)) {
            return response()->json(['ok' => false, 'error' => 'Invalid format']);
        }

        $id = (int) $matches[1];
        $reason = $matches[2];

        Log::info('Telegram reject processed', ['id' => $id, 'reason' => $reason]);

        return $this->processRejection($id, $reason);
    }

    /**
     * Process rejection with reason
     */
    protected function processRejection(int $id, string $reason): Response
    {
        $recharge = RechargeRequest::with(['user'])->find($id);

        if (!$recharge) {
            return response()->json(['ok' => false, 'error' => 'Request not found']);
        }

        if ($recharge->status !== 'pending') {
            return response()->json(['ok' => false, 'error' => 'Already processed']);
        }

        try {
            DB::beginTransaction();

            $user = $recharge->user;

            // Update recharge status
            $recharge->status = 'rejected';
            $recharge->rejection_reason = $reason;
            $recharge->rejected_at = now();
            $recharge->save();

            // Create notification for user
            Notification::create([
                'user_id' => $user->id,
                'type' => 'recharge_rejected',
                'title' => 'تم رفض طلب الشحن',
                'message' => 'تم رفض طلب الشحن مبلغ ' . number_format($recharge->amount, 2) . ' شيكل. السبب: ' . $reason,
                'data' => [
                    'amount' => $recharge->amount,
                    'recharge_id' => $recharge->id,
                    'reason' => $reason,
                ],
            ]);

            DB::commit();

            // Send confirmation to group
            $telegram = new TelegramService();
            $telegram->sendMessage("❌ تم رفض طلب الشحن\n\n👤 المستخدم: {$user->name}\n💰 المبلغ: {$recharge->amount} شيكل\n❌ السبب: {$reason}");

            Log::info('Telegram reject completed', ['id' => $id]);

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Telegram reject failed', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Answer callback query
     */
    protected function answerCallback(string $callbackId, string $text): void
    {
        $settings = TelegramSettings::first();
        if (!$settings || empty($settings->bot_token)) {
            return;
        }

        try {
            \Illuminate\Support\Facades\Http::post(
                "https://api.telegram.org/bot{$settings->bot_token}/answerCallbackQuery",
                [
                    'callback_query_id' => $callbackId,
                    'text' => $text,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Telegram answerCallback failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Set webhook URL (called by admin)
     */
    public function setWebhook(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $settings = TelegramSettings::first();
        if (!$settings || empty($settings->bot_token)) {
            return response()->json(['error' => 'Bot token not configured'], 400);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::post(
                "https://api.telegram.org/bot{$settings->bot_token}/setWebhook",
                [
                    'url' => $request->input('url'),
                ]
            );

            if ($response->successful()) {
                return response()->json(['ok' => true, 'message' => 'Webhook set successfully']);
            }

            return response()->json(['error' => 'Failed to set webhook'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}