<?php

namespace App\Services;

use App\Models\TelegramSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Telegram Service for sending recharge notifications
 *
 * Usage:
 * app(TelegramService::class)->sendRechargeNotification($rechargeRequest);
 */
class TelegramService
{
    protected string $botToken = '';

    protected string $chatId = '';

    protected bool $enabled = false;

    public function __construct()
    {
        $this->loadSettings();
    }

    /**
     * Load settings from database
     */
    protected function loadSettings(): void
    {
        $settings = TelegramSettings::first();

        if ($settings) {
            $this->botToken = $settings->bot_token ?? '';
            $this->chatId = $settings->chat_id ?? '';
            $this->enabled = (bool) ($settings->notifications_enabled ?? false);

            Log::info('TelegramService: Settings loaded', [
                'has_token' => ! empty($this->botToken),
                'has_chat_id' => ! empty($this->chatId),
                'enabled' => $this->enabled,
                'token_preview' => substr($this->botToken, 0, 10).'...',
            ]);

            // Validate token format
            if (! empty($this->botToken) && ! preg_match('/^\d+:[A-Za-z0-9_-]+$/', $this->botToken)) {
                Log::error('TelegramService: Invalid token format! Expected format: 123456789:ABCdefGHIjklMNOpqRSTUvwxyz');
            }
        } else {
            Log::warning('TelegramService: No settings found in database');
        }
    }

    /**
     * Check if service is ready (public for debugging)
     */
    public function checkReady(): array
    {
        return [
            'has_token' => ! empty($this->botToken),
            'has_chat_id' => ! empty($this->chatId),
            'enabled' => $this->enabled,
            'token_preview' => substr($this->botToken, 0, 10) ?? '',
            'chat_id' => $this->chatId ?? '',
        ];
    }

    /**
     * Check if service is ready
     */
    protected function isReady(): bool
    {
        if (empty($this->botToken)) {
            Log::error('TelegramService: Bot token is empty');

            return false;
        }

        if (empty($this->chatId)) {
            Log::error('TelegramService: Chat ID is empty');

            return false;
        }

        if (! $this->enabled) {
            Log::warning('TelegramService: Notifications are disabled');

            return false;
        }

        return true;
    }

    /**
     * Send text message to Telegram
     */
    public function sendMessage(string $text, ?array $keyboard = null): bool
    {
        Log::info('TelegramService: sendMessage called', [
            'text_length' => strlen($text),
            'has_keyboard' => ! empty($keyboard),
        ]);

        if (! $this->isReady()) {
            return false;
        }

        try {
            $data = [
                'chat_id' => $this->chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ];

            if ($keyboard) {
                $data['reply_markup'] = json_encode($keyboard);
            }

            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            Log::info("TelegramService: Calling API: $url");

            $response = Http::timeout(30)->post($url, $data);

            $body = $response->body();
            $status = $response->status();

            Log::info('TelegramService: API response', [
                'status' => $status,
                'body' => $body,
            ]);

            if ($response->successful()) {
                Log::info('TelegramService: Message sent successfully');

                return true;
            } else {
                Log::error('TelegramService: API failed', ['response' => $body]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('TelegramService: Exception', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Send photo to Telegram using local file
     */
    public function sendPhoto(string $photoPath, string $caption = ''): bool
    {
        Log::info('TelegramService: sendPhoto called', [
            'photoPath' => $photoPath,
        ]);

        if (! $this->isReady()) {
            Log::warning('TelegramService: isReady returned false, skipping photo');

            return false;
        }

        // Find the file
        $fullPath = $photoPath;
        if (! file_exists($fullPath)) {
            $fullPath = storage_path("app/public/{$photoPath}");
        }

        if (! file_exists($fullPath)) {
            Log::error('TelegramService: Photo file not found', ['path' => $fullPath]);

            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendPhoto";
            Log::info('TelegramService: Sending photo via sendPhoto API');

            $response = Http::timeout(60)
                ->attach('photo', file_get_contents($fullPath), 'proof.jpg')
                ->post($url, [
                    'chat_id' => $this->chatId,
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                ]);

            $body = $response->body();
            Log::info('TelegramService: Photo API response', [
                'status' => $response->status(),
                'success' => $response->successful(),
                'body' => $body,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('TelegramService: Photo exception', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Send photo by public URL
     */
    public function sendPhotoByUrl(string $photoUrl, string $caption = ''): bool
    {
        Log::info('TelegramService: sendPhotoByUrl called', ['url' => $photoUrl]);

        if (! $this->isReady()) {
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendPhoto";

            $response = Http::timeout(30)
                ->post($url, [
                    'chat_id' => $this->chatId,
                    'photo' => $photoUrl,
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                ]);

            Log::info('TelegramService: Photo URL response', [
                'status' => $response->status(),
                'success' => $response->successful(),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('TelegramService: Photo URL exception', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Send recharge notification
     */
    public function sendRechargeNotification($rechargeRequest): void
    {
        Log::info('TelegramService: sendRechargeNotification STARTED', [
            'id' => $rechargeRequest->id,
            'amount' => $rechargeRequest->amount,
        ]);

        if (! $this->isReady()) {
            Log::warning('TelegramService: Not ready, skipping notification');

            return;
        }

        // Get relations
        $user = $rechargeRequest->user;
        $paymentMethod = $rechargeRequest->paymentMethod;

        $userName = $user?->name ?? 'غير معروف';
        $userPhone = $user?->phone ?? 'غير معروف';
        $amount = $rechargeRequest->amount;
        $methodName = $paymentMethod?->name ?? 'غير محددة';
        $senderName = $rechargeRequest->sender_name ?? 'غير معروف';
        $senderPhone = $rechargeRequest->sender_phone ?? 'غير معروف';
        $date = $rechargeRequest->created_at?->format('Y-m-d H:i:s') ?? 'now';

        // Build message
        $text = "🆕 طلب شحن جديد\n\n";
        $text .= "━━━━━━━━━━━━━━━\n";
        $text .= "👤 المستخدم: {$userName}\n";
        $text .= "📱 الجوال: {$userPhone}\n";
        $text .= "💰 المبلغ: {$amount} شيكل\n";
        $text .= "🏦 طريقة الدفع: {$methodName}\n";
        $text .= "👤 اسم المحول: {$senderName}\n";
        $text .= "📞 رقم الجوال: {$senderPhone}\n";
        $text .= "━━━━━━━━━━━━━━━\n";
        $text .= "🕐 التاريخ: {$date}";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ موافقة', 'callback_data' => 'approve_'.$rechargeRequest->id],
                    ['text' => '❌ رفض', 'callback_data' => 'reject_'.$rechargeRequest->id],
                ],
            ],
        ];

        // Send message
        $messageSent = $this->sendMessage($text, $keyboard);

        if (! $messageSent) {
            Log::error('TelegramService: Failed to send main message');

            return;
        }

        // Send proof image
        if ($rechargeRequest->proof_image) {
            $proofPath = $rechargeRequest->proof_image;
            $caption = "📎 صورة إثبات الدفع - {$senderName}";

            $photoSent = $this->sendPhoto($proofPath, $caption);

            if ($photoSent) {
                Log::info('TelegramService: Photo sent successfully');
            } else {
                Log::warning('TelegramService: Failed to send photo');
            }
        }

        Log::info('TelegramService: sendRechargeNotification COMPLETED');
    }

    /**
     * Send approval notification
     */
    public function sendApprovalNotification($rechargeRequest): void
    {
        Log::info('TelegramService: sendApprovalNotification called');

        if (! $this->isReady()) {
            return;
        }

        $user = $rechargeRequest->user;
        $text = "✅ تم قبول طلب الشحن\n\n";
        $text .= "━━━━━━━━━━━━━━━\n";
        $text .= '👤 المستخدم: '.($user?->name ?? 'غير معروف')."\n";
        $text .= "💰 المبلغ: {$rechargeRequest->amount} شيكل\n";
        $text .= "━━━━━━━━━━━━━━━\n";
        $text .= '✅ تمت الموافقة';

        $this->sendMessage($text);
    }

    /**
     * Send rejection notification
     */
    public function sendRejectionNotification($rechargeRequest): void
    {
        Log::info('TelegramService: sendRejectionNotification called');

        if (! $this->isReady()) {
            return;
        }

        $user = $rechargeRequest->user;
        $reason = $rechargeRequest->rejection_reason ?? 'غير محدد';

        $text = "❌ تم رفض طلب الشحن\n\n";
        $text .= "━━━━━━━━━━━━━━━\n";
        $text .= '👤 المستخدم: '.($user?->name ?? 'غير معروف')."\n";
        $text .= "💰 المبلغ: {$rechargeRequest->amount} شيكل\n";
        $text .= "━━━━━━━━━━━━━━━\n";
        $text .= "❌ السبب: {$reason}";

        $this->sendMessage($text);
    }
}
