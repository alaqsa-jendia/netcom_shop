<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\TelegramCallbackController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('auth.login');
});

Route::get('/login', function () {
    return redirect()->route('auth.login');
})->name('login');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Push notifications
        Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe'])->name('push.subscribe');
        Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('push.unsubscribe');
        Route::post('/push/send', [PushSubscriptionController::class, 'sendNotification'])->name('push.send');

        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('create_user');
        Route::post('/users/create', [AdminController::class, 'createUser']);
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('edit_user');
        Route::post('/users/{id}/edit', [AdminController::class, 'editUser']);
        Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('delete_user');
        Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('toggle_user');

        Route::get('/admins', [AdminController::class, 'admins'])->name('admins');
        Route::get('/admins/create', [AdminController::class, 'createAdmin'])->name('create_admin');
        Route::post('/admins/create', [AdminController::class, 'createAdmin']);
        Route::get('/admins/{id}/edit', [AdminController::class, 'editAdmin'])->name('edit_admin');
        Route::post('/admins/{id}/edit', [AdminController::class, 'editAdmin']);
        Route::post('/admins/{id}/delete', [AdminController::class, 'deleteAdmin'])->name('delete_admin');

        Route::get('/packages', [AdminController::class, 'packages'])->name('packages');
        Route::get('/packages/create', [AdminController::class, 'createPackage'])->name('create_package');
        Route::post('/packages/create', [AdminController::class, 'createPackage']);
        Route::get('/packages/{id}/edit', [AdminController::class, 'editPackage'])->name('edit_package');
        Route::post('/packages/{id}/edit', [AdminController::class, 'editPackage']);
        Route::post('/packages/{id}/delete', [AdminController::class, 'deletePackage'])->name('delete_package');

        Route::get('/cards', [AdminController::class, 'cards'])->name('cards');
        Route::get('/cards/archive', [AdminController::class, 'cardsArchive'])->name('cards_archive');
        Route::post('/cards/import', [AdminController::class, 'importCards'])->name('import_cards');
        Route::post('/cards/clear', [AdminController::class, 'clearCards'])->name('clear_cards');
        Route::post('/cards/archive/clear', [AdminController::class, 'clearArchive'])->name('clear_archive');

        Route::get('/payment-methods', [AdminController::class, 'paymentMethods'])->name('payment_methods');
        Route::get('/payment-methods/create', [AdminController::class, 'createPaymentMethod'])->name('create_payment_method');
        Route::post('/payment-methods/create', [AdminController::class, 'createPaymentMethod']);
        Route::get('/payment-methods/{id}/edit', [AdminController::class, 'editPaymentMethod'])->name('edit_payment_method');
        Route::post('/payment-methods/{id}/edit', [AdminController::class, 'editPaymentMethod']);
        Route::post('/payment-methods/{id}/toggle', [AdminController::class, 'togglePaymentMethod'])->name('toggle_payment_method');
        Route::post('/payment-methods/{id}/delete', [AdminController::class, 'deletePaymentMethod'])->name('delete_payment_method');

        Route::get('/recharge-requests', [AdminController::class, 'rechargeRequests'])->name('recharge_requests');
        Route::get('/recharge-requests/{id}/details', [AdminController::class, 'rechargeRequestDetails'])->name('recharge_details');
        Route::get('/recharge-requests/{id}/approve', [AdminController::class, 'approveRecharge'])->name('approve_recharge');
        Route::post('/recharge-requests/{id}/reject', [AdminController::class, 'rejectRecharge'])->name('reject_recharge');
        Route::post('/recharge-requests/{id}/delete', [AdminController::class, 'deleteRechargeRequest'])->name('delete_recharge');

        Route::get('/chat', [AdminController::class, 'chat'])->name('chat');
        Route::post('/chat/send', [AdminController::class, 'sendChatMessage'])->name('send_chat');

        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'settings']);
    });
});

Route::middleware(['auth:web'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-cards', [UserController::class, 'myCards'])->name('my_cards');
    Route::get('/my-cards/archive', [UserController::class, 'myCardsArchive'])->name('my_cards_archive');
    Route::post('/my-cards/archive/clear', [UserController::class, 'clearMyCardsArchive'])->name('my_cards_archive_clear');
    Route::post('/my-cards/{id}/use', [UserController::class, 'markCardAsUsed'])->name('mark_card_used');
    Route::post('/buy-package', [UserController::class, 'buyPackage'])->name('buy_package');
    Route::get('/recharge', [UserController::class, 'recharge'])->name('recharge');
    Route::post('/recharge', [UserController::class, 'submitRecharge']);
    Route::get('/support', [UserController::class, 'support'])->name('support');
    Route::post('/support/send', [UserController::class, 'sendMessage'])->name('send_message');
    Route::get('/notifications', [UserController::class, 'getNotifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [UserController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [UserController::class, 'markAllNotificationsRead'])->name('notifications.read_all');
});

use App\Models\Notification;
use Illuminate\Support\Facades\Http;

// Telegram Webhook Routes (public - Telegram will call these)
Route::post('/telegram/webhook', [TelegramCallbackController::class, 'webhook'])->middleware('csrf.except');
Route::post('/telegram/reject', [TelegramCallbackController::class, 'rejectWithReason'])->middleware('csrf.except');

// Admin route to set webhook URL
Route::post('/telegram/set-webhook', [TelegramCallbackController::class, 'setWebhook']);

// Test route - manual Telegram test
Route::get('/telegram/test', function () {
    $response = Http::post(
        'https://api.telegram.org/bot'.env('TELEGRAM_BOT_TOKEN').'/sendMessage',
        [
            'chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
            'text' => '🔥 Telegram test from Laravel is working',
        ]
    );

    return $response->json();
});

// Debug route to check notifications
Route::get('/debug/notifications', function () {
    $userId = request()->get('user_id', 1);
    $notifications = Notification::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

    return response()->json([
        'user_id' => $userId,
        'count' => $notifications->count(),
        'notifications' => $notifications,
    ]);
})->middleware('auth:web');

// Debug route - create test notification
Route::get('/debug/create-notification', function () {
    if (! auth('web')->check()) {
        return response()->json(['error' => 'Not authenticated']);
    }

    $user = auth('web')->user();

    $notification = Notification::create([
        'user_id' => $user->id,
        'type' => 'test',
        'title' => 'Test Notification',
        'message' => 'This is a test notification',
        'is_read' => false,
    ]);

    return response()->json([
        'success' => true,
        'notification_id' => $notification->id,
        'user_id' => $user->id,
    ]);
})->middleware('auth:web');
