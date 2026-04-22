<?php

namespace App\Http\Controllers;

use App\Imports\CardsImport;
use App\Models\Admin;
use App\Models\Card;
use App\Models\Chat;
use App\Models\Notification;
use App\Models\Package;
use App\Models\PaymentMethod;
use App\Models\RechargeRequest;
use App\Models\TelegramSettings;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('success', 'مرحباً بك يا مدير');
        }

        return redirect()->back()->withErrors(['error' => 'اسم المستخدم أو كلمة المرور غير صحيحة'])->withInput();
    }

    public function dashboard()
    {
        $stats = [
            'users_count' => User::count(),
            'admins_count' => Admin::count(),
            'pending_requests' => RechargeRequest::where('status', 'pending')->count(),
            'approved_requests' => RechargeRequest::where('status', 'approved')->count(),
            'rejected_requests' => RechargeRequest::where('status', 'rejected')->count(),
            'total_transfers' => RechargeRequest::where('status', 'approved')->sum('amount'),
            'total_sales' => Card::where('status', 'sold')->count(),
            'cards_remaining' => Card::where('status', 'available')->count(),
        ];

        $pendingRequests = RechargeRequest::where('status', 'pending')
            ->with(['user', 'paymentMethod'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingRequests'));
    }

    public function users()
    {
        $users = User::with(['cards', 'rechargeRequests'])
            ->latest()
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function editUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'balance' => 'required|numeric|min:0',
                'status' => 'required|in:active,suspended',
                'password' => 'nullable|string|min:8',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->only(['name', 'balance', 'status']);

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('admin.users')->with('success', 'تم تحديث المستخدم');
        }

        return view('admin.edit_user', compact('user'));
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف المستخدم',
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'تم حذف المستخدم');
    }

    public function toggleUserStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'suspended' : 'active';
        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث حالة المستخدم',
                'is_active' => $user->status === 'active',
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث حالة المستخدم');
    }

    public function createUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => [
                    'required',
                    'unique:users,phone',
                    'regex:/^(059|056)[0-9]{7}$/',
                ],
                'password' => [
                    'required',
                    'min:8',
                    'regex:/^(?=.*[A-Z])(?=.*[0-9]).+$/',
                    'not_regex:/^[0-9]+$/',
                    'confirmed',
                ],
                'balance' => 'nullable|numeric|min:0',
                'status' => 'required|in:active,suspended',
                'email' => 'nullable|email|max:255|unique:users,email',
            ], [
                'name.required' => 'الاسم مطلوب',
                'phone.required' => 'رقم الجوال مطلوب',
                'phone.unique' => 'رقم الجوال مستخدم مسبقاً',
                'phone.regex' => 'رقم الجوال يجب أن يبدأ بـ 059 أو 056 ويكون 10 أرقام',
                'password.required' => 'كلمة المرور مطلوبة',
                'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
                'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وأرقام',
                'password.not_regex' => 'كلمة المرور لا يمكن أن تكون أرقام فقط',
                'password.confirmed' => 'كلمتا المرور غير متطابقتين',
                'balance.numeric' => 'الرصيد يجب أن يكون رقمياً',
                'status.required' => 'الحالة مطلوبة',
                'status.in' => 'قيمة الحالة غير صحيحة',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->only(['name', 'phone', 'balance', 'status']);

            // Set email - if not provided, use phone@netcom.local
            $data['email'] = $request->email ?: $request->phone.'@netcom.local';

            $data['password'] = Hash::make($request->password);
            $data['balance'] = $request->balance ?? 0;

            User::create($data);

            return redirect()->route('admin.users')->with('success', 'تم إنشاء المستخدم بنجاح');
        }

        return view('admin.create_user');
    }

    public function admins()
    {
        $admins = Admin::latest()->get();

        return view('admin.admins', compact('admins'));
    }

    public function createAdmin(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|unique:admins,username',
                'name' => 'required|string',
                'password' => 'required|string|min:8',
                'role' => 'required|in:super_admin,admin',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            Admin::create([
                'username' => $request->username,
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => true,
            ]);

            return redirect()->route('admin.admins')->with('success', 'تم إنشاء المدير');
        }

        return view('admin.create_admin');
    }

    public function deleteAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        if ($admin->id === 1) {
            return redirect()->back()->with('error', 'لا يمكن حذف المدير الأساسي');
        }
        $admin->delete();

        return redirect()->route('admin.admins')->with('success', 'تم حذف المدير');
    }

    public function editAdmin(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'role' => 'required|in:super_admin,admin',
                'password' => 'nullable|string|min:8',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = [
                'name' => $request->name,
                'role' => $request->role,
            ];

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            $admin->update($data);

            return redirect()->route('admin.admins')->with('success', 'تم تحديث المدير');
        }

        return view('admin.edit_admin', compact('admin'));
    }

    public function packages()
    {
        $packages = Package::latest()->get();

        return view('admin.packages', compact('packages'));
    }

    public function createPackage(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:1',
                'icon' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $validator->errors()->first(),
                    ], 422);
                }

                return redirect()->back()->withErrors($validator)->withInput();
            }

            $package = Package::create($request->all());

            if ($request->expectsJson()) {
                $html = view('partials.package_row', compact('package'))->render();

                return response()->json([
                    'status' => 'success',
                    'message' => 'تم إنشاء الباقة',
                    'item_html' => $html,
                    'item' => $package,
                ]);
            }

            return redirect()->route('admin.packages')->with('success', 'تم إنشاء الباقة');
        }

        return view('admin.create_package');
    }

    public function editPackage(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:1',
                'icon' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $package->update($request->all());

            return redirect()->route('admin.packages')->with('success', 'تم تحديث الباقة');
        }

        return view('admin.edit_package', compact('package'));
    }

    public function deletePackage(Request $request, $id)
    {
        $package = Package::findOrFail($id);
        $package->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف الباقة',
            ]);
        }

        return redirect()->route('admin.packages')->with('success', 'تم حذف الباقة');
    }

    public function cards()
    {
        $packages = Package::all();
        $cards = Card::with(['package', 'user'])
            ->where('status', '!=', 'sold')
            ->where('status', '!=', 'used')
            ->when(request('package_id'), function ($query) {
                return $query->where('package_id', request('package_id'));
            })
            ->latest()
            ->paginate(50);

        return view('admin.cards', compact('cards', 'packages'));
    }

    public function cardsArchive()
    {
        $packages = Package::all();
        $cards = Card::with(['package', 'user'])
            ->whereIn('status', ['sold', 'used'])
            ->when(request('package_id'), function ($query) {
                return $query->where('package_id', request('package_id'));
            })
            ->latest()
            ->paginate(50);

        return view('admin.cards_archive', compact('cards', 'packages'));
    }

    public function clearArchive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'nullable|exists:packages,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $query = Card::whereIn('status', ['sold', 'used']);

        if ($request->package_id) {
            $query->where('package_id', $request->package_id);
        }

        $count = $query->count();
        $query->delete();

        return redirect()->back()->with('success', 'تم حذف '.$count.' بطاقة من الأرشيف');
    }

    public function importCards(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:packages,id',
            'excel_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            Excel::import(new CardsImport($request->package_id), $request->file('excel_file'));

            DB::commit();

            return redirect()->route('admin.cards')->with('success', 'تم استيراد البطاقات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'حدث خطأ أثناء الاستيراد: '.$e->getMessage());
        }
    }

    public function clearCards(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:packages,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        Card::where('package_id', $request->package_id)
            ->where('status', 'available')
            ->delete();

        return redirect()->route('admin.cards')->with('success', 'تم تفريغ المخزون');
    }

    public function paymentMethods()
    {
        $methods = PaymentMethod::orderBy('order')->get();

        return view('admin.payment_methods', compact('methods'));
    }

    public function createPaymentMethod(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'account_name' => 'required|string',
                'account_number' => 'required|string',
                'logo' => 'nullable|image|max:1024',
                'qr_code' => 'nullable|image|max:1024',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->except(['_token']);

            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            }
            if ($request->hasFile('qr_code')) {
                $data['qr_code'] = $request->file('qr_code')->store('qrcodes', 'public');
            }

            $data['order'] = PaymentMethod::max('order') + 1;
            PaymentMethod::create($data);

            return redirect()->route('admin.payment_methods')->with('success', 'تم إضافة طريقة الدفع');
        }

        return view('admin.create_payment_method');
    }

    public function editPaymentMethod(Request $request, $id)
    {
        $method = PaymentMethod::findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'account_name' => 'required|string',
                'account_number' => 'required|string',
                'logo' => 'nullable|image|max:1024',
                'qr_code' => 'nullable|image|max:1024',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->except(['_token']);

            if ($request->hasFile('logo')) {
                if ($method->logo) {
                    Storage::disk('public')->delete($method->logo);
                }
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            }
            if ($request->hasFile('qr_code')) {
                if ($method->qr_code) {
                    Storage::disk('public')->delete($method->qr_code);
                }
                $data['qr_code'] = $request->file('qr_code')->store('qrcodes', 'public');
            }

            $method->update($data);

            return redirect()->route('admin.payment_methods')->with('success', 'تم تحديث طريقة الدفع');
        }

        return view('admin.edit_payment_method', compact('method'));
    }

    public function deletePaymentMethod($id)
    {
        $method = PaymentMethod::findOrFail($id);
        if ($method->logo) {
            Storage::disk('public')->delete($method->logo);
        }
        if ($method->qr_code) {
            Storage::disk('public')->delete($method->qr_code);
        }
        $method->delete();

        return redirect()->route('admin.payment_methods')->with('success', 'تم حذف طريقة الدفع');
    }

    public function togglePaymentMethod($id)
    {
        $method = PaymentMethod::findOrFail($id);
        $method->is_active = ! $method->is_active;
        $method->save();

        $message = $method->is_active ? 'تم تفعيل طريقة الدفع' : 'تم تعطيل طريقة الدفع';

        return redirect()->route('admin.payment_methods')->with('success', $message);
    }

    public function rechargeRequests()
    {
        $requests = RechargeRequest::with(['user', 'paymentMethod'])
            ->latest()
            ->paginate(20);

        return view('admin.recharge_requests', compact('requests'));
    }

    public function approveRecharge($id)
    {
        $recharge = RechargeRequest::with(['user', 'paymentMethod'])->findOrFail($id);
        $user = $recharge->user;

        DB::beginTransaction();
        try {
            $user->balance += $recharge->amount;
            $user->save();

            $recharge->status = 'approved';
            $recharge->approved_at = now();
            $recharge->save();

            // Create notification
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => 'recharge_approved',
                'title' => 'تم قبول طلب الشحن',
                'message' => 'تم إضافة '.number_format($recharge->amount, 2).' شيكل إلى رصيدك',
                'is_read' => false,
                'data' => [
                    'amount' => $recharge->amount,
                    'recharge_id' => $recharge->id,
                ],
            ]);

            Log::info('Notification created for recharge approval', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'amount' => $recharge->amount,
                'is_read' => $notification->is_read,
            ]);

            // Send Telegram notification
            $telegram = new TelegramService;
            $settings = TelegramSettings::first();
            if ($settings && $settings->notifications_enabled) {
                $telegram->sendApprovalNotification($recharge);
            }

            DB::commit();

            return redirect()->back()->with('success', 'تم الموافقة على الشحن وإضافة الرصيد');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'حدث خطأ');
        }
    }

    public function rejectRecharge(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:10',
        ], [
            'reason.required' => 'سبب الرفض مطلوب',
            'reason.min' => 'السبب يجب أن يكون 10 أحرف على الأقل',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $rechargeRequest = RechargeRequest::with(['user', 'paymentMethod'])->findOrFail($id);
        $rechargeRequest->status = 'rejected';
        $rechargeRequest->rejected_at = now();
        $rechargeRequest->rejection_reason = $request->reason;
        $rechargeRequest->save();

        // Create notification
        Notification::create([
            'user_id' => $rechargeRequest->user_id,
            'type' => 'recharge_rejected',
            'title' => 'تم رفض طلب الشحن',
            'message' => 'تم رفض طلب الشحن مبلغ '.number_format($rechargeRequest->amount, 2).' شيكل. السبب: '.$request->reason,
            'data' => [
                'amount' => $rechargeRequest->amount,
                'recharge_id' => $rechargeRequest->id,
                'reason' => $request->reason,
            ],
        ]);

        // Send Telegram notification
        $telegram = new TelegramService;
        $settings = TelegramSettings::first();
        if ($settings && $settings->notifications_enabled) {
            $telegram->sendRejectionNotification($rechargeRequest);
        }

        return redirect()->back()->with('success', 'تم رفض الطلب وإشعار المستخدم');
    }

    public function deleteRechargeRequest($id)
    {
        $rechargeRequest = RechargeRequest::findOrFail($id);

        // Delete proof image if exists
        if ($rechargeRequest->proof_image) {
            $path = storage_path('app/public/'.$rechargeRequest->proof_image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $rechargeRequest->delete();

        return redirect()->back()->with('success', 'تم حذف الطلب');
    }

    public function rechargeRequestDetails($id)
    {
        $rechargeRequest = RechargeRequest::with(['user', 'paymentMethod'])->findOrFail($id);

        $proofImage = null;
        $storedPath = $rechargeRequest->proof_image;

        if ($storedPath) {
            try {
                // Get direct file path from storage
                $fullPath = storage_path('app/public/'.$storedPath);
                if (file_exists($fullPath)) {
                    // Convert to base64 for direct display
                    $imageData = base64_encode(file_get_contents($fullPath));
                    $mimeType = mime_content_type($fullPath);
                    $proofImage = 'data:'.$mimeType.';base64,'.$imageData;
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return response()->json([
            'user_name' => $rechargeRequest->user?->name ?? 'غير موجود',
            'user_phone' => $rechargeRequest->user?->phone ?? 'غير موجود',
            'amount' => number_format($rechargeRequest->amount, 2),
            'payment_method' => $rechargeRequest->paymentMethod?->name ?? 'غير موجودة',
            'sender_name' => $rechargeRequest->sender_name,
            'sender_phone' => $rechargeRequest->sender_phone,
            'created_at' => $rechargeRequest->created_at->format('Y-m-d H:i'),
            'proof_image' => $proofImage,
            'stored_path' => $storedPath,
        ]);
    }

    public function chat()
    {
        $users = User::whereHas('chats')->with(['chats' => function ($q) {
            $q->latest()->first();
        }])->get();

        $selectedUser = null;
        $chats = [];

        if (request('user_id')) {
            $selectedUser = User::findOrFail(request('user_id'));
            $chats = Chat::where('user_id', $selectedUser->id)
                ->orderBy('created_at', 'asc')
                ->get();

            Chat::where('user_id', $selectedUser->id)
                ->where('sender_type', 'user')
                ->update(['is_read' => true]);
        }

        return view('admin.chat', compact('users', 'selectedUser', 'chats'));
    }

    public function sendChatMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $admin = Auth::guard('admin')->user();

        Chat::create([
            'user_id' => $request->user_id,
            'admin_id' => $admin->id,
            'message' => $request->message,
            'sender_type' => 'admin',
            'is_read' => false,
        ]);

        return redirect()->route('admin.chat', ['user_id' => $request->user_id])->with('success', 'تم إرسال الرسالة');
    }

    public function settings(Request $request)
    {
        $telegram = TelegramSettings::first();

        if ($request->isMethod('post')) {
            // Determine which form was submitted based on presence of fields
            $isStoreSettings = $request->has('system_name') || $request->hasFile('logo');
            $isTelegramSettings = $request->has('bot_token');
            $isContactSettings = $request->has('contact_phone') || $request->has('whatsapp_number');

            // Prepare base data
            $data = [];

            // Handle logo upload (store settings)
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($telegram && $telegram->logo) {
                    Storage::disk('public')->delete($telegram->logo);
                }
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            } elseif ($request->has('delete_logo') && $telegram && $telegram->logo) {
                Storage::disk('public')->delete($telegram->logo);
                $data['logo'] = null;
            }

            // Store settings fields
            if ($request->filled('system_name')) {
                $data['system_name'] = $request->system_name;
            }

            // Clear old fields that are no longer used
            if ($telegram && $telegram->logo2) {
                Storage::disk('public')->delete($telegram->logo2);
                $data['logo2'] = null;
            }
            if ($telegram && $telegram->system_name_ar) {
                $data['system_name_ar'] = null;
            }

            // Telegram settings fields
            if ($request->filled('bot_token')) {
                $data['bot_token'] = $request->bot_token;
            }
            if ($request->has('chat_id')) {
                $data['chat_id'] = $request->chat_id;
            }
            if ($request->has('notifications_enabled')) {
                $data['notifications_enabled'] = true;
            } else {
                $data['notifications_enabled'] = false;
            }

            // Contact settings fields
            if ($request->filled('contact_phone')) {
                $data['contact_phone'] = $request->contact_phone;
            }
            if ($request->filled('whatsapp_number')) {
                $data['whatsapp_number'] = $request->whatsapp_number;
            }

            if ($telegram) {
                $telegram->update($data);
            } else {
                // Set defaults for missing fields
                $defaultData = array_merge([
                    'bot_token' => '',
                    'chat_id' => null,
                    'notifications_enabled' => true,
                    'contact_phone' => null,
                    'whatsapp_number' => null,
                    'system_name' => null,
                    'logo' => null,
                ], $data);
                TelegramSettings::create($defaultData);
            }

            return redirect()->back()->with('success', 'تم حفظ الإعدادات');
        }

        return view('admin.settings', compact('telegram'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
