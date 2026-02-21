<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helpers\CaptchaHelper;
use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * 顯示登入頁面
     */
    public function showLogin()
    {
        $captchaSvg = CaptchaHelper::generate('captcha_superadmin');
        return view('superadmin.login', compact('captchaSvg'));
    }

    public function refreshCaptcha()
    {
        $svg = CaptchaHelper::generate('captcha_superadmin');
        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    /**
     * 處理登入
     */
    public function login(Request $request)
    {
        // 鎖定檢查
        $throttleKey = 'login_superadmin:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['email' => "登入失敗次數過多，請 {$seconds} 秒後再試"]);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'captcha' => 'required',
        ], ['captcha.required' => '請輸入驗證碼']);

        // 驗證碼檢查
        if (!CaptchaHelper::verify($request->captcha, 'captcha_superadmin')) {
            RateLimiter::hit($throttleKey, 900);
            return back()->withErrors(['captcha' => '驗證碼錯誤']);
        }

        // 查找超級管理員
        $admin = SuperAdmin::where('email', $request->email)->first();

        // 驗證帳號密碼
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            RateLimiter::hit($throttleKey, 900);
            $remaining = max(0, 3 - RateLimiter::attempts($throttleKey));
            $msg = $remaining > 0 ? "帳號或密碼錯誤（還可嘗試 {$remaining} 次）" : '帳號或密碼錯誤';
            return back()->withErrors(['email' => $msg]);
        }

        // 檢查是否啟用
        if (!$admin->isActive()) {
            return back()->withErrors(['email' => '此帳號已被停用']);
        }

        // 更新最後登入資訊
        $admin->updateLastLogin($request->ip());

        // 登入
        Auth::guard('superadmin')->login($admin, $request->filled('remember'));
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('superadmin.dashboard'));
    }

    /**
     * 處理登出
     */
    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login');
    }

    /**
     * API 登入（返回 Token）
     */
    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = SuperAdmin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => '帳號或密碼錯誤'
            ], 401);
        }

        if (!$admin->isActive()) {
            return response()->json([
                'message' => '此帳號已被停用'
            ], 403);
        }

        // 更新最後登入資訊
        $admin->updateLastLogin($request->ip());

        // 建立 API Token
        $token = $admin->createToken('superadmin-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }

    /**
     * API 登出
     */
    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => '登出成功'
        ]);
    }
}
