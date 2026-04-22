<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\Card;
use App\Models\Chat;
use App\Models\Notification;
use App\Models\Package;
use App\Models\PaymentMethod;
use App\Models\RechargeRequest;
use App\Models\TelegramSettings;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $packages = Package::where('is_active', true)->get();

        return view('user.dashboard', compact('user', 'packages'));
    }

    public function myCards()
    {
        $user = Auth::user();
        $cards = Card::where('user_id', $user->id)
            ->whereIn('status', ['sold', 'used'])
            ->orderByRaw("CASE WHEN status = 'sold' THEN 0 ELSE 1 END")
            ->orderBy('sold_at', 'desc')
            ->get();

        return view('user.cards', compact('cards'));
    }

    public function myCardsArchive()
    {
        $user = Auth::user();
        $cards = Card::where('user_id', $user->id)
            ->where('status', 'used')
            ->orderBy('sold_at', 'desc')
            ->get();

        return view('user.cards_archive', compact('cards'));
    }

    public function clearMyCardsArchive()
    {
        $user = Auth::user();

        $count = Card::where('user_id', $user->id)
            ->where('status', 'used')
            ->count();

        Card::where('user_id', $user->id)
            ->where('status', 'used')
            ->delete();

        return redirect()->back()->with('success', 'تم حذف '.$count.' بطاقة من الأرشيف');
    }

    public function markCardAsUsed($id)
    {
        $user = Auth::user();
        $card = Card::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'sold')
            ->firstOrFail();

        $card->status = 'used';
        $card->save();

        return redirect()->back()->with('success', 'تم تحديد البطاقة كمستخدمة');
    }

    public function buyPackage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:packages,id',
            'quantity' => 'required|integer|min:1',
        ], [
            'package_id.required' => 'الباقة مطلوبة',
            'package_id.exists' => 'الباقة غير موجودة',
            'quantity.required' => 'الكمية مطلوبة',
            'quantity.min' => 'الكمية يجب أن تكون 1 على الأقل',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $package = Package::findOrFail($request->package_id);
        $quantity = $request->quantity;
        $totalPrice = $package->price * $quantity;

        if ($user->balance < $totalPrice) {
            return response()->json(['error' => 'رصيدك غير كافي'], 400);
        }

        $availableCards = Card::where('package_id', $package->id)
            ->where('status', 'available')
            ->take($quantity)
            ->get();

        if ($availableCards->count() < $quantity) {
            return response()->json(['error' => 'البطاقات المتوفرة غير كافية'], 400);
        }

        DB::beginTransaction();
        try {
            $user->balance -= $totalPrice;
            $user->save();

            foreach ($availableCards as $card) {
                $card->user_id = $user->id;
                $card->status = 'sold';
                $card->sold_at = now();
                $card->save();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'تم الشراء بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'حدث خطأ أثناء الشراء'], 500);
        }
    }

    public function recharge()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('order')->get();

        return view('user.recharge', compact('paymentMethods'));
    }

    public function submitRecharge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => [
                'required',
                'exists:payment_methods,id',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:10',
            ],
            'sender_name' => [
                'required',
                'min:4',
            ],
            'sender_phone' => [
                'required',
                'regex:/^(059|056)[0-9]{7}$/',
            ],
            'proof_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ], [
            'payment_method_id.required' => 'يرجى اختيار طريقة الدفع',
            'amount.required' => 'المبلغ مطلوب',
            'amount.min' => 'أقل مبلغ للتشحيل شيكل واحد',
            'sender_name.required' => 'اسم المحول مطلوب',
            'sender_name.min' => 'الاسم يجب أن يكون 4 أحرف على الأقل',
            'sender_phone.required' => 'رقم الجوال مطلوب',
            'sender_phone.regex' => 'رقم الجوال يجب أن يبدأ بـ 059 أو 056 ويكون 10 أرقام',
            'proof_image.required' => 'صورة الإشعار مطلوبة',
            'proof_image.image' => 'يجب أن تكون صورة',
            'proof_image.mimes' => 'صيغة الصورة يجب أن تكون jpg أو png',
            'proof_image.max' => 'حجم الصورة الأقصى 2 ميجابايت',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        $imagePath = $request->file('proof_image')->store('proofs', 'public');

        // Create recharge request
        $rechargeRequest = RechargeRequest::create([
            'user_id' => $user->id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $request->amount,
            'sender_name' => $request->sender_name,
            'sender_phone' => $request->sender_phone,
            'proof_image' => $imagePath,
            'status' => 'pending',
        ]);

        // Send Telegram notification to admins about new recharge request
        try {
            $telegram = new TelegramService;
            $telegram->sendRechargeNotification($rechargeRequest);
        } catch (\Exception $e) {
            \Log::error('Telegram notification failed: '.$e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'تم إرسال طلب الشحن بنجاح، بانتظار المراجعة');
    }

    public function support()
    {
        $user = Auth::user();
        $chats = Chat::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $telegram = TelegramSettings::first();

        $contactInfo = [
            'phone' => '',
            'whatsapp' => '',
        ];

        if ($telegram) {
            $contactInfo['phone'] = $telegram->contact_phone ?? '';
            $contactInfo['whatsapp'] = $telegram->whatsapp_number ?? '';
        }

        return view('user.support', compact('chats', 'contactInfo'));
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ], [
            'message.required' => 'الرسالة مطلوبة',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        $chat = Chat::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'sender_type' => 'user',
            'is_read' => false,
        ]);

        event(new NewChatMessage($chat));

        return redirect()->route('support')->with('success', 'تم إرسال الرسالة');
    }

    public function getNotifications()
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json($notifications);
    }

    public function markNotificationRead($id)
    {
        $user = Auth::user();
        $notification = Notification::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)->where('is_read', false)->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
