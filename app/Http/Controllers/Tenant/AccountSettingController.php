<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountSettingController extends Controller
{
    /**
     * 顯示帳號設定頁面
     */
    public function index()
    {
        $user = Auth::user();
        return view('tenant.settings.account', compact('user'));
    }

    /**
     * 更新個人資料
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
        ], [
            'name.required' => '姓名為必填',
            'email.required' => 'Email 為必填',
            'email.email' => 'Email 格式錯誤',
            'email.unique' => 'Email 已被使用',
        ]);

        $user->update($validated);

        return back()->with('success', '個人資料已更新');
    }

    /**
     * 更新密碼
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => '請輸入目前密碼',
            'password.required' => '請輸入新密碼',
            'password.confirmed' => '密碼確認不一致',
            'password.min' => '密碼至少需要 8 個字元',
        ]);

        $user = Auth::user();

        // 驗證目前密碼
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => '目前密碼錯誤']);
        }

        // 更新密碼
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', '密碼已更新');
    }

    /**
     * 更新通知設定
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $settings = $request->validate([
            'email_notifications' => 'boolean',
            'project_notifications' => 'boolean',
            'finance_notifications' => 'boolean',
            'system_notifications' => 'boolean',
        ]);

        // 儲存通知設定到 user settings JSON 欄位
        $user->settings = array_merge($user->settings ?? [], [
            'notifications' => $settings
        ]);
        $user->save();

        return back()->with('success', '通知設定已更新');
    }
}
