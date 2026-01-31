@extends('layouts.tenant')

@section('title', '代碼管理設定')

@section('page-title', '代碼管理設定')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.settings.index') }}" class="hover:text-primary">系統設定</a>
        &gt; 代碼管理設定
    </p>
</div>

<!-- 頁面標題 -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">代碼管理設定</h1>
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">統一管理所有模組的代碼生成規則</p>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('tenant.settings.codes.update') }}">
    @csrf

    <!-- 客戶/廠商代碼設定 -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            客戶/廠商代碼設定
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    代碼前綴
                </label>
                <input type="text" name="company_code_prefix" 
                       value="{{ old('company_code_prefix', \App\Models\TenantSetting::get('company_code_prefix', 'C')) }}"
                       maxlength="5"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">例如：C、COM、COMP</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    數字長度
                </label>
                <input type="number" name="company_code_length" 
                       value="{{ old('company_code_length', \App\Models\TenantSetting::get('company_code_length', 4)) }}"
                       min="1" max="10"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">數字部分的位數（1-10）</p>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="company_code_auto" value="1"
                           {{ old('company_code_auto', \App\Models\TenantSetting::get('company_code_auto', true)) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">啟用自動生成</span>
                </label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 ml-6">新增時自動產生代碼</p>
            </div>
        </div>

        <!-- 預覽 -->
        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">預覽範例：</p>
            <p class="text-lg font-mono font-semibold text-gray-900 dark:text-white">
                {{ \App\Models\TenantSetting::get('company_code_prefix', 'C') }}{{ str_pad('1', \App\Models\TenantSetting::get('company_code_length', 4), '0', STR_PAD_LEFT) }}
                <span class="text-sm text-gray-500 dark:text-gray-400 font-normal ml-2">
                    (例：{{ \App\Models\TenantSetting::get('company_code_prefix', 'C') }}{{ str_pad('1', \App\Models\TenantSetting::get('company_code_length', 4), '0', STR_PAD_LEFT) }}, 
                    {{ \App\Models\TenantSetting::get('company_code_prefix', 'C') }}{{ str_pad('2', \App\Models\TenantSetting::get('company_code_length', 4), '0', STR_PAD_LEFT) }}, 
                    {{ \App\Models\TenantSetting::get('company_code_prefix', 'C') }}{{ str_pad('3', \App\Models\TenantSetting::get('company_code_length', 4), '0', STR_PAD_LEFT) }})
                </span>
            </p>
        </div>
    </div>

    <!-- 部門代碼設定 -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            部門代碼設定
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    代碼前綴
                </label>
                <input type="text" name="department_code_prefix" 
                       value="{{ old('department_code_prefix', \App\Models\TenantSetting::get('department_code_prefix', 'D')) }}"
                       maxlength="5"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    數字長度
                </label>
                <input type="number" name="department_code_length" 
                       value="{{ old('department_code_length', \App\Models\TenantSetting::get('department_code_length', 3)) }}"
                       min="1" max="10"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="department_code_auto" value="1"
                           {{ old('department_code_auto', \App\Models\TenantSetting::get('department_code_auto', true)) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">啟用自動生成</span>
                </label>
            </div>
        </div>

        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">預覽範例：</p>
            <p class="text-lg font-mono font-semibold text-gray-900 dark:text-white">
                {{ \App\Models\TenantSetting::get('department_code_prefix', 'D') }}{{ str_pad('1', \App\Models\TenantSetting::get('department_code_length', 3), '0', STR_PAD_LEFT) }}
            </p>
        </div>
    </div>

    <!-- 專案代碼設定 -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            專案代碼設定
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    代碼前綴
                </label>
                <input type="text" name="project_code_prefix" 
                       value="{{ old('project_code_prefix', \App\Models\TenantSetting::get('project_code_prefix', 'P')) }}"
                       maxlength="5"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    數字長度
                </label>
                <input type="number" name="project_code_length" 
                       value="{{ old('project_code_length', \App\Models\TenantSetting::get('project_code_length', 4)) }}"
                       min="1" max="10"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="project_code_auto" value="1"
                           {{ old('project_code_auto', \App\Models\TenantSetting::get('project_code_auto', true)) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">啟用自動生成</span>
                </label>
            </div>
        </div>

        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">預覽範例：</p>
            <p class="text-lg font-mono font-semibold text-gray-900 dark:text-white">
                {{ \App\Models\TenantSetting::get('project_code_prefix', 'P') }}{{ str_pad('1', \App\Models\TenantSetting::get('project_code_length', 4), '0', STR_PAD_LEFT) }}
            </p>
        </div>
    </div>

    <!-- 按鈕 -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('tenant.settings.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
            取消
        </a>
        <button type="submit" 
                class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
            儲存設定
        </button>
    </div>
</form>
@endsection
