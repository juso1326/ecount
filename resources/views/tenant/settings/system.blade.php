@extends('layouts.tenant')

@section('title', '系統設定')

@section('page-title', '系統設定')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.settings.index') }}" class="hover:text-primary">系統設定</a> 
        <span class="mx-2">/</span>
        系統設定
    </p>
</div>

<!-- 頁面標題 -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">系統設定</h1>
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">設定系統基本偏好與顯示格式</p>
</div>

<!-- 成功訊息 -->
@if(session('success'))
    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<!-- 設定表單 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <form action="{{ route('tenant.settings.system.update') }}" method="POST">
        @csrf
        @method('POST')
        
        <div class="p-6 space-y-6">
            <!-- 日期格式設定 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    日期顯示格式
                </label>
                <select name="date_format" 
                        class="w-full md:w-1/2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="Y-m-d" {{ ($dateFormat == 'Y-m-d') ? 'selected' : '' }}>
                        YYYY-MM-DD ({{ date('Y-m-d') }})
                    </option>
                    <option value="Y/m/d" {{ ($dateFormat == 'Y/m/d') ? 'selected' : '' }}>
                        YYYY/MM/DD ({{ date('Y/m/d') }})
                    </option>
                    <option value="Y.m.d" {{ ($dateFormat == 'Y.m.d') ? 'selected' : '' }}>
                        YYYY.MM.DD ({{ date('Y.m.d') }})
                    </option>
                    <option value="m/d/Y" {{ ($dateFormat == 'm/d/Y') ? 'selected' : '' }}>
                        MM/DD/YYYY ({{ date('m/d/Y') }})
                    </option>
                    <option value="d/m/Y" {{ ($dateFormat == 'd/m/Y') ? 'selected' : '' }}>
                        DD/MM/YYYY ({{ date('d/m/Y') }})
                    </option>
                    <option value="Ymd" {{ ($dateFormat == 'Ymd') ? 'selected' : '' }}>
                        YYYYMMDD ({{ date('Ymd') }})
                    </option>
                </select>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">選擇系統中日期的顯示格式</p>
            </div>

            <!-- 時間格式設定 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    時間顯示格式
                </label>
                <select name="time_format" 
                        class="w-full md:w-1/2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="H:i:s" {{ ($timeFormat == 'H:i:s') ? 'selected' : '' }}>
                        24小時制 ({{ date('H:i:s') }})
                    </option>
                    <option value="H:i" {{ ($timeFormat == 'H:i') ? 'selected' : '' }}>
                        24小時制 (不含秒) ({{ date('H:i') }})
                    </option>
                    <option value="h:i:s A" {{ ($timeFormat == 'h:i:s A') ? 'selected' : '' }}>
                        12小時制 ({{ date('h:i:s A') }})
                    </option>
                    <option value="h:i A" {{ ($timeFormat == 'h:i A') ? 'selected' : '' }}>
                        12小時制 (不含秒) ({{ date('h:i A') }})
                    </option>
                </select>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">選擇系統中時間的顯示格式</p>
            </div>

            <!-- 時區設定 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    時區
                </label>
                <select name="timezone" 
                        class="w-full md:w-1/2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="Asia/Taipei" {{ ($timezone == 'Asia/Taipei') ? 'selected' : '' }}>
                        台北 (GMT+8)
                    </option>
                    <option value="Asia/Shanghai" {{ ($timezone == 'Asia/Shanghai') ? 'selected' : '' }}>
                        上海 (GMT+8)
                    </option>
                    <option value="Asia/Hong_Kong" {{ ($timezone == 'Asia/Hong_Kong') ? 'selected' : '' }}>
                        香港 (GMT+8)
                    </option>
                    <option value="Asia/Tokyo" {{ ($timezone == 'Asia/Tokyo') ? 'selected' : '' }}>
                        東京 (GMT+9)
                    </option>
                    <option value="UTC" {{ ($timezone == 'UTC') ? 'selected' : '' }}>
                        UTC (GMT+0)
                    </option>
                </select>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">選擇系統使用的時區</p>
            </div>
        </div>

        <!-- 表單按鈕 -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600 flex justify-end gap-3">
            <a href="{{ route('tenant.settings.index') }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                返回
            </a>
            <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg">
                儲存設定
            </button>
        </div>
    </form>
</div>

<!-- 說明 -->
<div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">
        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
        說明
    </h3>
    <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1 ml-5 list-disc">
        <li>日期格式變更後，會套用至所有日期欄位的顯示</li>
        <li>時間格式變更後，會套用至所有時間欄位的顯示</li>
        <li>時區設定會影響系統記錄的時間戳記</li>
    </ul>
</div>
@endsection
