@extends('layouts.superadmin')

@section('title', 'Error Log')
@section('page-title', 'Error Log')

@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-2.5 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- ── 頂部統計卡片 ── --}}
@php
    $statCards = [
        ['level'=>'ERROR',    'label'=>'錯誤',   'color'=>'red',    'icon'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['level'=>'CRITICAL', 'label'=>'嚴重',   'color'=>'rose',   'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['level'=>'WARNING',  'label'=>'警告',   'color'=>'amber',  'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['level'=>'INFO',     'label'=>'資訊',   'color'=>'blue',   'icon'=>'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['level'=>'DEBUG',    'label'=>'除錯',   'color'=>'gray',   'icon'=>'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
    ];
    $colorMap = [
        'red'   => ['bg'=>'bg-red-50 dark:bg-red-900/20',   'border'=>'border-red-200 dark:border-red-800',   'text'=>'text-red-700 dark:text-red-300',   'badge'=>'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300',   'icon'=>'text-red-400'],
        'rose'  => ['bg'=>'bg-rose-50 dark:bg-rose-900/20', 'border'=>'border-rose-200 dark:border-rose-800', 'text'=>'text-rose-700 dark:text-rose-300', 'badge'=>'bg-rose-100 dark:bg-rose-900 text-rose-700',                  'icon'=>'text-rose-400'],
        'amber' => ['bg'=>'bg-amber-50 dark:bg-amber-900/20','border'=>'border-amber-200 dark:border-amber-800','text'=>'text-amber-700 dark:text-amber-300','badge'=>'bg-amber-100 text-amber-700',                                'icon'=>'text-amber-400'],
        'blue'  => ['bg'=>'bg-blue-50 dark:bg-blue-900/20', 'border'=>'border-blue-200 dark:border-blue-800', 'text'=>'text-blue-700 dark:text-blue-300', 'badge'=>'bg-blue-100 text-blue-700',                                   'icon'=>'text-blue-400'],
        'gray'  => ['bg'=>'bg-gray-50 dark:bg-gray-700/30', 'border'=>'border-gray-200 dark:border-gray-600', 'text'=>'text-gray-600 dark:text-gray-400', 'badge'=>'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300','icon'=>'text-gray-400'],
    ];
@endphp
<div class="grid grid-cols-5 gap-2 mb-4">
    @foreach($statCards as $sc)
    @php $c = $colorMap[$sc['color']]; $cnt = $levelCounts[$sc['level']] ?? 0; @endphp
    <a href="{{ route('superadmin.error-log.index', array_filter(['level'=>$sc['level'],'tenant'=>$tenantId])) }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-lg border {{ $c['bg'] }} {{ $c['border'] }} hover:shadow-sm transition group {{ $level === $sc['level'] ? 'ring-2 ring-offset-1 ring-indigo-400' : '' }}">
        <svg class="w-5 h-5 flex-shrink-0 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $sc['icon'] }}"/>
        </svg>
        <div>
            <p class="text-lg font-bold leading-none {{ $c['text'] }}">{{ $cnt }}</p>
            <p class="text-xs {{ $c['text'] }} opacity-70 mt-0.5">{{ $sc['label'] }}</p>
        </div>
    </a>
    @endforeach
</div>

<div class="flex gap-4">

    {{-- ── 左側：日誌來源 ── --}}
    <div class="w-52 flex-shrink-0">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-4">
            <div class="flex items-center gap-2 px-3 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/60">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">日誌來源</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700/50 max-h-[65vh] overflow-y-auto">
                {{-- 中央系統 --}}
                <a href="{{ route('superadmin.error-log.index') }}"
                   class="flex items-center gap-2 px-3 py-2.5 text-sm transition-colors
                          {{ !$tenantId
                             ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-semibold border-l-2 border-indigo-500'
                             : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/60 border-l-2 border-transparent' }}">
                    <svg class="w-4 h-4 flex-shrink-0 {{ !$tenantId ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                    </svg>
                    <span class="flex-1 truncate">中央系統</span>
                </a>
                {{-- 各租戶 --}}
                @foreach($tenants as $t)
                @php $isActive = $tenantId === $t['id']; @endphp
                <a href="{{ route('superadmin.error-log.index', ['tenant' => $t['id']]) }}"
                   class="flex items-center gap-2 px-3 py-2.5 text-sm transition-colors
                          {{ $isActive
                             ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-semibold border-l-2 border-indigo-500'
                             : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/60 border-l-2 border-transparent' }}">
                    <span class="w-4 h-4 flex-shrink-0 flex items-center justify-center">
                        @if($t['has_log'])
                        <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-indigo-500' : 'bg-gray-300 dark:bg-gray-500' }}"></span>
                        @else
                        <span class="w-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700"></span>
                        @endif
                    </span>
                    <span class="flex-1 truncate text-xs" title="{{ $t['name'] ?: $t['id'] }}">
                        {{ $t['name'] ?: $t['id'] }}
                    </span>
                    <span class="text-xs {{ $t['has_log'] ? 'text-gray-400' : 'text-gray-300 dark:text-gray-600' }} flex-shrink-0">
                        {{ $t['has_log'] ? $t['log_size'] : '—' }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── 右側：主內容 ── --}}
    <div class="flex-1 min-w-0">

        {{-- 標題 + 清空 --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">{{ $logLabel }}</h2>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    {{ $logSize }}
                    <span class="text-gray-300">·</span>
                    顯示 <strong class="text-gray-600 dark:text-gray-300">{{ count($entries) }}</strong> 筆
                    @if($level)
                    <span class="px-1.5 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 rounded font-medium">{{ $level }}</span>
                    @endif
                </div>
            </div>
            <form action="{{ route('superadmin.error-log.clear') }}" method="POST" data-ev-skip
                  onsubmit="return confirm('確定要清空「{{ $logLabel }}」日誌？此操作無法復原。')">
                @csrf
                @if($tenantId)<input type="hidden" name="tenant" value="{{ $tenantId }}">@endif
                <button type="submit"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-500 hover:bg-red-600 active:bg-red-700 rounded-lg transition shadow-sm">
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
                @if($tenantId)<input type="hidden" name="tenant" value="{{ $tenantId }}">@endif
                <div class="flex-1 min-w-36">
                    <label class="block text-xs text-gray-400 mb-1">🔍 關鍵字</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="搜尋訊息..."
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-1.5 text-sm focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">📊 級別</label>
                    <select name="level" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-1.5 text-sm focus:ring-1 focus:ring-indigo-400">
                        <option value="">全部</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl }}" {{ $level === $lvl ? 'selected' : '' }}>
                                {{ $lvl }} {{ isset($levelCounts[$lvl]) ? '('.$levelCounts[$lvl].')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="px-4 py-1.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition">
                    搜尋
                </button>
                @if($level || $search)
                <a href="{{ route('superadmin.error-log.index', $tenantId ? ['tenant'=>$tenantId] : []) }}"
                   class="px-3 py-1.5 text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    ✕ 清除篩選
                </a>
                @endif
            </form>
        </div>

        {{-- 日誌列表 --}}
        @if(count($entries) === 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 p-16 text-center">
            <svg class="w-14 h-14 mx-auto text-gray-200 dark:text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm font-medium text-gray-400 dark:text-gray-500">
                {{ ($level || $search) ? '沒有符合條件的日誌記錄' : '此日誌目前為空' }}
            </p>
            @if($level || $search)
            <a href="{{ route('superadmin.error-log.index', $tenantId ? ['tenant'=>$tenantId] : []) }}"
               class="mt-3 inline-block text-xs text-indigo-500 hover:underline">清除篩選條件</a>
            @endif
        </div>
        @else
        <div class="space-y-1">
            @foreach($entries as $entry)
            @php
                $levelStyle = [
                    'ERROR'     => ['row'=>'border-l-red-500 bg-white dark:bg-gray-800 hover:bg-red-50/40 dark:hover:bg-red-900/10',   'badge'=>'bg-red-500 text-white',          'dot'=>'bg-red-500',    'pre'=>'text-red-300'],
                    'CRITICAL'  => ['row'=>'border-l-rose-600 bg-white dark:bg-gray-800 hover:bg-rose-50/40 dark:hover:bg-rose-900/10','badge'=>'bg-rose-600 text-white',         'dot'=>'bg-rose-600',   'pre'=>'text-rose-300'],
                    'EMERGENCY' => ['row'=>'border-l-red-700 bg-white dark:bg-gray-800',                                               'badge'=>'bg-red-700 text-white',          'dot'=>'bg-red-700',    'pre'=>'text-red-200'],
                    'ALERT'     => ['row'=>'border-l-orange-500 bg-white dark:bg-gray-800 hover:bg-orange-50/40',                      'badge'=>'bg-orange-500 text-white',       'dot'=>'bg-orange-500', 'pre'=>'text-orange-300'],
                    'WARNING'   => ['row'=>'border-l-yellow-400 bg-white dark:bg-gray-800 hover:bg-yellow-50/30',                      'badge'=>'bg-yellow-400 text-yellow-900',  'dot'=>'bg-yellow-400', 'pre'=>'text-yellow-200'],
                    'NOTICE'    => ['row'=>'border-l-blue-400 bg-white dark:bg-gray-800 hover:bg-blue-50/30',                          'badge'=>'bg-blue-400 text-white',         'dot'=>'bg-blue-400',   'pre'=>'text-blue-200'],
                    'INFO'      => ['row'=>'border-l-emerald-400 bg-white dark:bg-gray-800 hover:bg-emerald-50/30',                    'badge'=>'bg-emerald-500 text-white',      'dot'=>'bg-emerald-400','pre'=>'text-emerald-300'],
                    'DEBUG'     => ['row'=>'border-l-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750',      'badge'=>'bg-gray-400 text-white',         'dot'=>'bg-gray-300',   'pre'=>'text-gray-400'],
                ];
                $s = $levelStyle[$entry['level']] ?? $levelStyle['DEBUG'];
            @endphp
            <div class="border border-gray-100 dark:border-gray-700 border-l-4 {{ $s['row'] }} rounded-lg overflow-hidden shadow-sm"
                 x-data="{ open: false }">
                <div class="flex items-center gap-3 px-3 py-2 cursor-pointer select-none"
                     @click="open = !open">
                    {{-- Level badge --}}
                    <span class="flex-shrink-0 min-w-[62px] text-center px-2 py-0.5 text-xs font-bold rounded-md {{ $s['badge'] }}">
                        {{ $entry['level'] }}
                    </span>
                    {{-- Time --}}
                    <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0 font-mono tabular-nums whitespace-nowrap">
                        {{ $entry['time'] }}
                    </span>
                    {{-- Message --}}
                    <p class="flex-1 text-xs text-gray-700 dark:text-gray-300 font-mono leading-snug truncate">
                        {{ Str::limit($entry['message'], 160) }}
                    </p>
                    {{-- Expand icon --}}
                    @if($entry['detail'])
                    <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-300 dark:text-gray-600 transition-transform duration-150"
                         :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    @else
                    <span class="w-3.5 h-3.5 flex-shrink-0"></span>
                    @endif
                </div>
                {{-- Detail panel --}}
                @if($entry['detail'])
                <div x-show="open" x-cloak
                     class="border-t border-gray-100 dark:border-gray-700 bg-[#0d1117] px-5 py-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full {{ $s['dot'] }}"></span>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Stack Trace</span>
                    </div>
                    <pre class="text-xs {{ $s['pre'] }} overflow-x-auto whitespace-pre-wrap break-words font-mono leading-relaxed">{{ $entry['message'] }}
{{ trim($entry['detail']) }}</pre>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>

@endsection
