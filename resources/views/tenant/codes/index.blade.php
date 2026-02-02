@extends('layouts.tenant')

@section('title', '代碼管理')
@section('page-title', '代碼管理')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8 ">
    @foreach($categories as $key => $name)
    <a href="{{ route('tenant.codes.category', $key) }}" 
       class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-lg bg-primary bg-opacity-10 flex items-center justify-center group-hover:bg-opacity-20 transition">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
            </div>
            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $codeCounts[$key] ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $name }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            @if($key === 'project_task')
                專案中的任務類型，用於專案管理和時間追蹤
            @elseif($key === 'project_type')
                專案的分類，用於專案篩選和統計分析
            @elseif($key === 'deduction_type')
                扣款項目類型，用於財務管理
            @else
                付款項目類型，用於財務管理
            @endif
        </p>
    </a>
    @endforeach
</div>

@endsection
