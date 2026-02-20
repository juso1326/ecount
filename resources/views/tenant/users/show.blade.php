@extends('layouts.tenant')

@section('title', '使用者詳情')

@section('content')
<div class="mb-3 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">使用者詳情</h1>
    <div class="space-x-2">
        <a href="{{ route('tenant.users.edit', $user) }}" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg">
            編輯
        </a>
        <a href="{{ route('tenant.users.index') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-bold py-2 px-4 rounded-lg">
            返回列表
        </a>
    </div>
</div>

<!-- 基本信息 -->
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-3 border border-gray-200 dark:border-gray-700">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">基本資訊</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">姓名</label>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $user->name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Email</label>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $user->email }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">狀態</label>
            <p class="mt-1">
                @if($user->is_active)
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        啟用
                    </span>
                @else
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        停用
                    </span>
                @endif
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">最後登入時間</label>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $user->last_login_at ? format_datetime($user->last_login_at) : '從未登入' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">建立時間</label>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format_datetime($user->created_at) }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">更新時間</label>
            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ format_datetime($user->updated_at) }}</p>
        </div>
    </div>
</div>

<!-- 參與專案 -->
@if($user->projects->count() > 0)
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-3 border border-gray-200 dark:border-gray-700">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">參與的專案</h2>
    
    @php
        $activeProjects = $user->projects->filter(fn($p) => in_array($p->status, ['planning', 'in_progress']));
        $completedProjects = $user->projects->filter(fn($p) => $p->status === 'completed');
    @endphp

    @if($activeProjects->count() > 0)
        <div class="mb-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">進行中的專案</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($activeProjects as $project)
                    <div class="border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <a href="{{ route('tenant.projects.show', $project) }}" class="text-lg font-medium text-primary hover:text-primary-dark">
                                    {{ $project->code }} - {{ $project->name }}
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $project->company->name }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-200 dark:bg-blue-700 text-blue-800 dark:text-blue-200">
                                @switch($project->status)
                                    @case('planning')
                                        規劃中
                                    @break
                                    @case('in_progress')
                                        進行中
                                    @break
                                    @default
                                        {{ $project->status }}
                                @endswitch
                            </span>
                        </div>
                        @if($project->pivot)
                            <p class="text-sm text-gray-600 dark:text-gray-400">角色：{{ $project->pivot->role ?? '成員' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">加入時間：{{ format_date($project->pivot->joined_at) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($completedProjects->count() > 0)
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">已完成的專案</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($completedProjects as $project)
                    <div class="border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <a href="{{ route('tenant.projects.show', $project) }}" class="text-lg font-medium text-gray-700 dark:text-gray-300 hover:text-primary">
                                    {{ $project->code }} - {{ $project->name }}
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $project->company->name }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                已完成
                            </span>
                        </div>
                        @if($project->pivot)
                            <p class="text-sm text-gray-600 dark:text-gray-400">角色：{{ $project->pivot->role ?? '成員' }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endif

@if($user->id !== auth()->id())
<div class="mt-6 flex justify-end">
    <form action="{{ route('tenant.users.destroy', $user) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
            刪除使用者
        </button>
    </form>
</div>
@endif
@endsection
