@extends('layouts.tenant')

@section('title', '財務設定')

@section('page-title', '財務設定')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">系統設定 &gt; 財務設定</p>
</div>

<!-- 頁面標題 -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">財務設定</h1>
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">設定財務相關的基本參數</p>
</div>

<!-- 成功訊息 -->
@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

<!-- 表單 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
    <form action="{{ route('tenant.settings.financial.update') }}" method="POST">
        @csrf

        <!-- 每月關帳日 -->
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                每月關帳日 <span class="text-red-500">*</span>
            </label>
            <select name="closing_day" 
                    class="w-full md:w-1/2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent @error('closing_day') border-red-500 @enderror"
                    required>
                <option value="">請選擇日期</option>
                @for($day = 1; $day <= 31; $day++)
                    <option value="{{ $day }}" {{ old('closing_day', $closingDay) == $day ? 'selected' : '' }}>
                        每月 {{ $day }} 號
                    </option>
                @endfor
            </select>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                💡 設定每月固定的關帳日期，用於財務報表的週期計算。若該月份沒有該日期（如 2 月 31 號），系統將自動使用該月最後一天。
            </p>
            @error('closing_day')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- 預設交易幣值 -->
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                預設交易幣值 <span class="text-red-500">*</span>
            </label>
            <select name="default_currency" 
                    class="w-full md:w-1/2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent @error('default_currency') border-red-500 @enderror"
                    required>
                <option value="">請選擇幣值</option>
                @foreach($currencies as $code => $name)
                    <option value="{{ $code }}" {{ old('default_currency', $defaultCurrency) === $code ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                💡 新增交易時將預設使用此幣值，您仍可在個別交易中修改。
            </p>
            @error('default_currency')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- 分隔線 -->
        <div class="border-t border-gray-200 dark:border-gray-600 my-6"></div>

        <!-- 其他財務設定連結 -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">進階設定</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- 支出項目管理 -->
                <a href="{{ route('tenant.expense-categories.index') }}" 
                   class="block p-4 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">支出項目管理</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">設定支出分類</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <!-- 稅款設定 -->
                <a href="{{ route('tenant.tax-settings.index') }}" 
                   class="block p-4 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">稅款設定</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">管理稅率項目</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <!-- 標籤管理 -->
                <a href="{{ route('tenant.tags.index') }}" 
                   class="block p-4 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">標籤管理</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">專案/客戶/成員標籤</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            </div>
        </div>

        <!-- 按鈕 -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
            <button type="submit" 
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
                儲存設定
            </button>
        </div>
    </form>
</div>
@endsection
