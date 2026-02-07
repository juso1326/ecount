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
        <div class="border-t border-gray-200 dark:border-gray-600 my-8"></div>

        <!-- 會計年度設定標題 -->
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">📅 會計年度設定</h2>

        <!-- 會計年度起始月份 -->
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                會計年度起始月份 <span class="text-red-500">*</span>
            </label>
            <select name="fiscal_year_start" 
                    class="w-full md:w-1/2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent @error('fiscal_year_start') border-red-500 @enderror"
                    required>
                <option value="">請選擇月份</option>
                @for($month = 1; $month <= 12; $month++)
                    <option value="{{ $month }}" {{ old('fiscal_year_start', $fiscalYearStart) == $month ? 'selected' : '' }}>
                        {{ $month }} 月 ({{ ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'][$month - 1] }})
                    </option>
                @endfor
            </select>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                💡 設定會計年度的起始月份。例如：選擇 4 月，則會計年度為 4 月～次年 3 月。預設為 1 月（曆年制）。
            </p>
            @error('fiscal_year_start')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- 預設帳務年度 -->
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                預設帳務年度 <span class="text-red-500">*</span>
            </label>
            <input type="number" 
                   name="default_fiscal_year" 
                   value="{{ old('default_fiscal_year', $defaultFiscalYear) }}"
                   min="2000" 
                   max="2100"
                   class="w-full md:w-1/2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent @error('default_fiscal_year') border-red-500 @enderror"
                   required>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                💡 查詢應收/應付帳款時，預設顯示此年度的資料。建議設定為當前年度：{{ date('Y') }} 年。
            </p>
            @error('default_fiscal_year')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- 年度設定說明卡片 -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">帳務年度功能說明</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                        <ul class="list-disc list-inside space-y-1">
                            <li>系統會自動從發票日期/付款日期提取年份作為帳務年度</li>
                            <li>應收/應付帳款頁面可切換不同年度查詢</li>
                            <li>新增資料時，系統會自動設定正確的年度</li>
                            <li>更新日期時，年度會自動同步更新</li>
                        </ul>
                    </div>
                </div>
            </div>
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
