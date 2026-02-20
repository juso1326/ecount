@extends('layouts.superadmin')

@section('title', 'Error Log')
@section('page-title', 'Error Log')

@section('content')

<!-- 統計列 -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-4">
    <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
        <span>日誌大小：<span class="font-semibold text-gray-900 dark:text-white">{{ $logSize }}</span></span>
        <span>顯示最新 <span class="font-semibold">{{ count($entries) }}</span> 筆</span>
    </div>
    <form action="{{ route('superadmin.error-log.clear') }}" method="POST">
        @csrf
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
            清空日誌
        </button>
    </form>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
    {{ session('success') }}
</div>
@endif

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs text-gray-500 mb-1">關鍵字搜尋</label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="搜尋錯誤訊息..."
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">級別</label>
            <select name="level" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                <option value="">全部級別</option>
                @foreach($levels as $lvl)
                    <option value="{{ $lvl }}" {{ $level === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg">
            搜尋
        </button>
        @if($level || $search)
            <a href="{{ route('superadmin.error-log.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200">
                清除
            </a>
        @endif
    </form>
</div>

<!-- 日誌列表 -->
@if(count($entries) === 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center text-gray-500 dark:text-gray-400">
        @if($level || $search)
            沒有符合條件的日誌
        @else
            日誌檔案為空
        @endif
    </div>
@else
<div class="space-y-2" x-data="{}">
    @foreach($entries as $i => $entry)
    @php
        $colors = [
            'ERROR'     => 'border-red-400 bg-red-50 dark:bg-red-900/10',
            'CRITICAL'  => 'border-red-600 bg-red-100 dark:bg-red-900/20',
            'EMERGENCY' => 'border-red-700 bg-red-100 dark:bg-red-900/20',
            'ALERT'     => 'border-orange-500 bg-orange-50 dark:bg-orange-900/10',
            'WARNING'   => 'border-yellow-400 bg-yellow-50 dark:bg-yellow-900/10',
            'NOTICE'    => 'border-blue-300 bg-blue-50 dark:bg-blue-900/10',
            'INFO'      => 'border-green-400 bg-green-50 dark:bg-green-900/10',
            'DEBUG'     => 'border-gray-300 bg-gray-50 dark:bg-gray-700/30',
        ];
        $badges = [
            'ERROR'     => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'CRITICAL'  => 'bg-red-200 text-red-900 dark:bg-red-800 dark:text-red-100',
            'EMERGENCY' => 'bg-red-300 text-red-900',
            'ALERT'     => 'bg-orange-100 text-orange-800',
            'WARNING'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'NOTICE'    => 'bg-blue-100 text-blue-800',
            'INFO'      => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'DEBUG'     => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        ];
        $color = $colors[$entry['level']] ?? 'border-gray-300 bg-gray-50';
        $badge = $badges[$entry['level']] ?? 'bg-gray-100 text-gray-700';
    @endphp
    <div class="border-l-4 rounded-lg {{ $color }} overflow-hidden" x-data="{ open: false }">
        <div class="flex items-start gap-3 px-4 py-3 cursor-pointer" @click="open = !open">
            <span class="flex-shrink-0 px-2 py-0.5 text-xs font-bold rounded {{ $badge }}">
                {{ $entry['level'] }}
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 mt-0.5">
                {{ $entry['time'] }}
            </span>
            <p class="flex-1 text-sm text-gray-900 dark:text-white font-mono leading-snug truncate">
                {{ Str::limit($entry['message'], 200) }}
            </p>
            @if($entry['detail'])
            <svg class="w-4 h-4 flex-shrink-0 text-gray-400 mt-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            @endif
        </div>
        @if($entry['detail'])
        <div x-show="open" x-cloak class="border-t border-gray-200 dark:border-gray-600 bg-gray-900 px-4 py-3">
            <pre class="text-xs text-green-300 overflow-x-auto whitespace-pre-wrap break-words font-mono leading-relaxed">{{ $entry['message'] }}
{{ $entry['detail'] }}</pre>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

@endsection
