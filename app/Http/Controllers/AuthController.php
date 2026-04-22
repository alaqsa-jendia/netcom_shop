<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                'regex:/^(059|056)[0-9]{7}$/',
            ],
            'password' => [
                'required',
                'min:8',
            ],
        ], [
            'phone.required' => 'رقم الجوال مطلوب',
            'phone.regex' => 'رقم الجوال يجب أن يبدأ بـ 059 أو 056 ويكون 10 أرقام',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 خانات على الأقل',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $credentials = [
            'phone' => $request->phone,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'مرحباً بك');
        }

        return redirect()->back()->withErrors(['error' => 'رقم الجوال أو كلمة المرور غير صحيحة'])->withInput();
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
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
            ],
        ], [
            'phone.required' => 'رقم الجوال مطلوب',
            'phone.regex' => 'رقم الجوال يجب أن يبدأ بـ 059 أو 056 ويكون 10 أرقام',
            'password.min' => 'كلمة المرور يجب أن تكون 8 خانات على الأقل',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وأرقام',
            'password.not_regex' => 'كلمة المرور لا يمكن أن تكون أرقام فقط',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->phone.'@netcom.local',
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'balance' => 0.00,
            'status' => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'تم إنشاء الحساب بنجاح');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
