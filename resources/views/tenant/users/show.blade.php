@extends('layouts.tenant')

@section('title', '使用者詳情')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">專案帳戶管理 &gt; <a href="{{ route('tenant.users.index') }}" class="text-primary hover:text-primary-dark">使用者管理</a> &gt; 使用者詳情</p>
</div>

<!-- 頁面標題 -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">使用者詳情</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $user->name }} 的帳號資訊</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('tenant.users.edit', $user) }}" 
           class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
            編輯
        </a>
        <a href="{{ route('tenant.users.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 font-medium py-2 px-4 rounded-lg">
            返回列表
        </a>
    </div>
</div>

<!-- 基本資訊 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">基本資訊</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">姓名</label>
                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->name }}</p>
            </div>

            @if($user->short_name)
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">簡稱</label>
                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->short_name }}</p>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">登入帳號 (Email)</label>
                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->email }}</p>
            </div>

            @if($user->backup_email)
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">備份 Email</label>
                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->backup_email }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- 角色與權限 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">角色與權限</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">角色層級</label>
                <p class="mt-1">
                    @if($user->hasRole('admin'))
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            系統管理員
                        </span>
                    @elseif($user->hasRole('manager'))
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                            總管理/主管
                        </span>
                    @elseif($user->hasRole('accountant'))
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            會計
                        </span>
                    @elseif($user->hasRole('employee'))
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            成員
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                            未設定
                        </span>
                    @endif
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">關聯員工</label>
                <p class="mt-1 text-base text-gray-900 dark:text-white">
                    @if($user->company)
                        <a href="{{ route('tenant.companies.edit', $user->company) }}" 
                           class="text-primary hover:text-primary-dark">
                            {{ $user->company->name }} 
                            <span class="text-gray-400">({{ $user->company->code }})</span>
                        </a>
                    @else
                        <span class="text-gray-400 dark:text-gray-500">-</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">狀態</label>
                <p class="mt-1">
                    @if($user->is_active)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            啟用
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            停用
                        </span>
                    @endif
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">權限期限</label>
                <p class="mt-1 text-base text-gray-900 dark:text-white">
                    @if($user->permission_start_date || $user->permission_end_date)
                        <div class="text-sm">
                            @if($user->permission_start_date)
                                <div>起：{{ $user->permission_start_date->format('Y/m/d') }}</div>
                            @endif
                            @if($user->permission_end_date)
                                <div class="{{ $user->permission_end_date->isPast() ? 'text-red-600 dark:text-red-400 font-medium' : '' }}">
                                    迄：{{ $user->permission_end_date->format('Y/m/d') }}
                                    @if($user->permission_end_date->isPast())
                                        <span class="ml-1">(已過期)</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <span class="text-gray-400 dark:text-gray-500">永久</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<!-- 登入資訊 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">登入資訊</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">最後登入時間</label>
                <p class="mt-1 text-base text-gray-900 dark:text-white">
                    {{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : '從未登入' }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">帳號建立時間</label>
                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->created_at->format('Y-m-d H:i:s') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">最後更新時間</label>
                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- 備註 -->
@if($user->note)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">備註</h3>
    </div>
    <div class="p-6">
        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $user->note }}</p>
    </div>
</div>
@endif

@php
    $isLastAdmin = false;
    if ($user->hasRole('admin')) {
        $adminCount = \App\Models\User::role('admin')->count();
        $isLastAdmin = ($adminCount <= 1);
    }
    $cannotDelete = ($user->id === auth()->id()) || $isLastAdmin;
@endphp

@if(!$cannotDelete)
<div class="mt-6 flex justify-end">
    <form action="{{ route('tenant.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('確定要刪除此使用者嗎？');">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg">
            刪除使用者
        </button>
    </form>
</div>
@else
<div class="mt-6 flex justify-end">
    <div class="text-sm">
        @if($user->id === auth()->id())
            <p class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg px-4 py-2">
                無法刪除自己的帳號
            </p>
        @elseif($isLastAdmin)
            <p class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-600 text-yellow-800 dark:text-yellow-200 rounded-lg px-4 py-2">
                此為最後一個系統管理員，無法刪除
            </p>
        @endif
    </div>
</div>
@endif
@endsection
