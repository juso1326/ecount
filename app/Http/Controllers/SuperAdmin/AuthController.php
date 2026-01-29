<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * 顯示登入頁面
     */
    public function showLogin()
    {
        return view('superadmin.login');
    }

    /**
     * 處理登入
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 查找超級管理員
        $admin = SuperAdmin::where('email', $request->email)->first();

        // 驗證帳號密碼
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['帳號或密碼錯誤'],
            ]);
        }

        // 檢查是否啟用
        if (!$admin->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['此帳號已被停用'],
            ]);
        }

        // 更新最後登入資訊
        $admin->updateLastLogin($request->ip());

        // 登入
        Auth::guard('superadmin')->login($admin, $request->filled('remember'));

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
