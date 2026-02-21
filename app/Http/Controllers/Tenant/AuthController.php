<?php

namespace App\Http\Controllers\Tenant;

use App\Helpers\CaptchaHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin(Request $request)
    {
        $captchaSvg  = CaptchaHelper::generate('captcha_tenant');
        $throttleKey = 'login_tenant:' . $request->ip();
        $isLocked    = RateLimiter::tooManyAttempts($throttleKey, 3);
        $lockSeconds = $isLocked ? RateLimiter::availableIn($throttleKey) : 0;
        $attempts    = RateLimiter::attempts($throttleKey);
        $remaining   = max(0, 3 - $attempts);
        return view('tenant.auth.login', compact('captchaSvg', 'isLocked', 'lockSeconds', 'attempts', 'remaining'));
    }

    public function refreshCaptcha()
    {
        $svg = CaptchaHelper::generate('captcha_tenant');
        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // 鎖定檢查
        $throttleKey = 'login_tenant:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['email' => "登入失敗次數過多，請 {$seconds} 秒後再試"])->withInput();
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required',
        ], [
            'email.required' => 'Email 為必填',
            'email.email' => 'Email 格式不正確',
            'password.required' => '密碼為必填',
            'captcha.required' => '請輸入驗證碼',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 驗證碼檢查（verify 會清除 session，showLogin redirect 後重新產生）
        if (!CaptchaHelper::verify($request->captcha, 'captcha_tenant')) {
            RateLimiter::hit($throttleKey, 900);
            return back()->withErrors(['captcha' => '驗證碼錯誤'])->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);
            Auth::user()->update(['last_login_at' => now()]);
            return redirect()->intended(route('tenant.dashboard'));
        }

        RateLimiter::hit($throttleKey, 900);
        $remaining = max(0, 3 - RateLimiter::attempts($throttleKey));
        $msg = $remaining > 0
            ? "Email 或密碼錯誤（還可嘗試 {$remaining} 次）"
            : 'Email 或密碼錯誤';
        return back()->withErrors(['email' => $msg])->withInput();
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tenant.login');
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('tenant.auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => '姓名為必填',
            'email.required' => 'Email 為必填',
            'email.email' => 'Email 格式不正確',
            'email.unique' => 'Email 已被使用',
            'password.required' => '密碼為必填',
            'password.min' => '密碼至少需要 8 個字元',
            'password.confirmed' => '密碼確認不一致',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('tenant.dashboard');
    }

    /**
     * API Login
     */
    public function apiLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '驗證失敗',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email 或密碼錯誤'
            ], 401);
        }

        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => '登入成功',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * API Logout
     */
    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => '登出成功'
        ]);
    }
}
