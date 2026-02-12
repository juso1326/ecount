@extends('layouts.tenant')

@section('title', '使用者管理')

@section('page-title', '使用者管理')

@section('content')
<div class="mb-2 flex justify-end items-center">
    <a href="{{ route('tenant.users.create') }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增使用者
    </a>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" action="{{ route('tenant.users.index') }}" class="flex gap-4 items-end">
        <!-- 搜尋框 -->
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="搜尋姓名、Email、員工編號..." 
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        
        <!-- 狀態篩選 -->
        <div>
            <select name="is_active" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">全部狀態</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>啟用</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>停用</option>
            </select>
        </div>
        
        <button type="submit" 
                class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
            搜尋
        </button>
        
        @if(request()->hasAny(['search', 'is_active']))
            <a href="{{ route('tenant.users.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                清除
            </a>
        @endif
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">編輯</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">員工編號</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">姓名</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">部門</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">參與專案</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">角色</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-2 whitespace-nowrap text-sm">
                    <a href="{{ route('tenant.users.edit', $user) }}" 
                       class="text-primary hover:text-primary-dark font-medium">
                        編輯
                    </a>
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $user->employee_id }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $user->name }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->email }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->department?->name }}
                </td>
                <td class="px-6 py-2 text-sm text-gray-500 dark:text-gray-400">
                    @if($user->projects && $user->projects->count() > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->projects->take(3) as $project)
                                <a href="{{ route('tenant.projects.show', $project) }}" 
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800"
                                   title="{{ $project->name }}">
                                    {{ $project->code }}
                                </a>
                            @endforeach
                            @if($user->projects->count() > 3)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                    +{{ $user->projects->count() - 3 }}
                                </span>
                            @endif
                        </div>
                    @else
                        <span class="text-gray-400 dark:text-gray-500">無</span>
                    @endif
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    @if($user->hasRole('admin'))
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            系統管理員
                        </span>
                    @elseif($user->hasRole('manager'))
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            總管理/主管
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            一般使用者
                        </span>
                    @endif
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm">
                    @if($user->is_active)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            啟用
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            停用
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    沒有找到任何使用者資料
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 分頁 -->
<div class="mt-6">
    {{ $users->appends(request()->except('page'))->links() }}
</div>
@endsection
