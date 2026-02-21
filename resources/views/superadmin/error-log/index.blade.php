@extends('layouts.superadmin')

@section('title', 'Error Log')
@section('page-title', 'Error Log')

@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
    {{ session('success') }}
</div>
@endif

<div class="flex gap-4">

    {{-- ── 左側：日誌來源切換 ── --}}
    <div class="w-56 flex-shrink-0">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-4">
            <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">日誌來源</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[70vh] overflow-y-auto">
                {{-- 中央系統 --}}
                <a href="{{ route('superadmin.error-log.index') }}"
                   class="flex items-center gap-2 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ !$tenantId ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-gray-700 dark:text-gray-300' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                    </svg>
                    <span class="flex-1 truncate">中央系統</span>
                    @if(!$tenantId)
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 flex-shrink-0"></span>
                    @endif
                </a>
                {{-- 各租戶 --}}
                @foreach($tenants as $t)
                <a href="{{ route('superadmin.error-log.index', ['tenant' => $t['id']]) }}"
                   class="flex items-center gap-2 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $tenantId === $t['id'] ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-gray-700 dark:text-gray-300' }}">
                    <svg class="w-4 h-4 flex-shrink-0 {{ $t['has_log'] ? 'text-gray-400' : 'text-gray-300 dark:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="flex-1 truncate" title="{{ $t['name'] }}">{{ $t['name'] ?: $t['id'] }}</span>
                    @if($t['has_log'])
                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $t['log_size'] }}</span>
                    @else
                        <span class="text-xs text-gray-300 dark:text-gray-600 flex-shrink-0">—</span>
                    @endif
                    @if($tenantId === $t['id'])
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 flex-shrink-0"></span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── 右側：主內容 ── --}}
    <div class="flex-1 min-w-0">

        {{-- 標題列 --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-3">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    {{ $logLabel }}
                </h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    大小：<span class="font-medium text-gray-700 dark:text-gray-200">{{ $logSize }}</span>
                    &nbsp;·&nbsp;
                    最新 <span class="font-medium">{{ count($entries) }}</span> 筆
                </span>
            </div>
            <form action="{{ route('superadmin.error-log.clear') }}" method="POST" data-ev-skip
                  onsubmit="return confirm('確定要清空「{{ $logLabel }}」日誌？')">
                @csrf
                @if($tenantId)
                <input type="hidden" name="tenant" value="{{ $tenantId }}">
                @endif
                <button type="submit"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    清空日誌
                </button>
            </form>
        </div>

        {{-- 搜尋列 --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-3 mb-3">
            <form method="GET" class="flex flex-wrap gap-2 items-end" data-ev-skip>
                @if($tenantId)
                <input type="hidden" name="tenant" value="{{ $tenantId }}">
                @endif
                <div class="flex-1 min-w-40">
                    <label class="block text-xs text-gray-500 mb-1">關鍵字</label>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="搜尋訊息..."
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-1.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">級別</label>
                    <select name="level" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-1.5 text-sm">
                        <option value="">全部</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl }}" {{ $level === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="px-4 py-1.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md">
                    搜尋
                </button>
                @if($level || $search)
                <a href="{{ route('superadmin.error-log.index', $tenantId ? ['tenant' => $tenantId] : []) }}"
                   class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200">
                    清除
                </a>
                @endif
            </form>
        </div>

        {{-- 日誌列表 --}}
        @if(count($entries) === 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                {{ ($level || $search) ? '沒有符合條件的日誌' : '此日誌為空' }}
            </p>
        </div>
        @else
        <div class="space-y-1.5">
            @foreach($entries as $entry)
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
                <div class="flex items-start gap-3 px-3 py-2.5 cursor-pointer select-none" @click="open = !open">
                    <span class="flex-shrink-0 px-2 py-0.5 text-xs font-bold rounded {{ $badge }}">
                        {{ $entry['level'] }}
                    </span>
                    <span class="text-xs text-gray-400 flex-shrink-0 mt-0.5 font-mono">
                        {{ $entry['time'] }}
                    </span>
                    <p class="flex-1 text-xs text-gray-900 dark:text-white font-mono leading-snug truncate">
                        {{ Str::limit($entry['message'], 180) }}
                    </p>
                    @if($entry['detail'])
                    <svg class="w-4 h-4 flex-shrink-0 text-gray-400 mt-0.5 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    @endif
                </div>
                @if($entry['detail'])
                <div x-show="open" x-cloak class="border-t border-gray-200 dark:border-gray-700 bg-gray-950 px-4 py-3">
                    <pre class="text-xs text-emerald-400 overflow-x-auto whitespace-pre-wrap break-words font-mono leading-relaxed">{{ $entry['message'] }}
{{ trim($entry['detail']) }}</pre>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

    </div>{{-- end right --}}
</div>{{-- end flex --}}

@endsection
