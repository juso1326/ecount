@extends('layouts.tenant')

@section('title', '系統設定')

@section('page-title', '系統設定')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">系統設定</p>
</div>

<!-- 頁面標題 -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">系統設定</h1>
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">管理系統各項設定與代碼規則</p>
</div>

<!-- 設定卡片 -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <!-- 公司設定 -->
    <a href="{{ route('tenant.settings.company') }}" 
       class="block bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md hover:border-primary dark:hover:border-primary transition-all">
        <div class="flex items-center mb-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">公司設定</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">客戶/廠商代碼生成規則設定</p>
    </a>

    <!-- 代碼管理設定 -->
    <a href="{{ route('tenant.settings.codes') }}" 
       class="block bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md hover:border-primary dark:hover:border-primary transition-all">
        <div class="flex items-center mb-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/20">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">代碼管理設定</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">統一管理所有模組的代碼生成規則</p>
    </a>

    <!-- 系統設定 -->
    <a href="{{ route('tenant.settings.system') }}" 
       class="block bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md hover:border-primary dark:hover:border-primary transition-all">
        <div class="flex items-center mb-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">系統設定</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">系統基本設定與偏好設定</p>
    </a>

    <!-- 帳號設定 -->
    <a href="{{ route('tenant.settings.account') }}" 
       class="block bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md hover:border-primary dark:hover:border-primary transition-all">
        <div class="flex items-center mb-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/20">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">帳號設定</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">個人帳號資訊與密碼設定</p>
    </a>
</div>
@endsection
