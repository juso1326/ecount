@extends('layouts.tenant')

@section('title', '使用者管理')

@section('page-title', '使用者管理')

@section('content')
<!-- 分頁資訊與操作按鈕 -->
<div class="mb-2 flex justify-between items-center">
    <div class="text-sm text-gray-600 dark:text-gray-400">
        @if($users->total() > 0)
            顯示第 <span class="font-medium">{{ $users->firstItem() }}</span> 
            到 <span class="font-medium">{{ $users->lastItem() }}</span> 筆，
            共 <span class="font-medium">{{ number_format($users->total()) }}</span> 筆
        @else
            <span>無資料</span>
        @endif
    </div>
    <div class="flex gap-2">
        @if($resignedCount > 0)
        <a href="{{ route('tenant.users.index', array_merge(request()->except('page'), ['show_resigned' => $showResigned ? '0' : '1'])) }}"
           class="flex items-center gap-1 text-sm font-medium py-2 px-4 rounded-lg border {{ $showResigned ? 'bg-amber-100 border-amber-400 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-gray-100 border-gray-300 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8m-9 4v4m4-4v4"/></svg>
            {{ $showResigned ? '隱藏離職' : '顯示離職' }}（{{ $resignedCount }}）
        </a>
        @endif
        <a href="{{ route('tenant.users.export') }}" 
           class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg shadow-sm">
            匯出 Excel
        </a>
        @can('users.create')
        <a href="{{ route('tenant.users.create') }}" 
           class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
            + 新增使用者
        </a>
        @endcan
    </div>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" action="{{ route('tenant.users.index') }}" class="space-y-4">
        <!-- 智能搜尋框 -->
        <div class="flex gap-2">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="🔍 智能搜尋：姓名/Email/員工編號..." 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-transparent text-base">
            </div>
            <button type="submit" 
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                搜尋
            </button>
            @if(request()->hasAny(['search', 'is_active']))
                <a href="{{ route('tenant.users.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                    清除
                </a>
            @endif
        </div>
        
        <!-- 進階篩選 -->
        <details class="group">
            <summary class="cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary">
                <span class="inline-block group-open:rotate-90 transition-transform">▶</span>
                進階篩選
            </summary>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <!-- 狀態篩選 -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">使用者狀態</label>
                    <select name="is_active" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">全部狀態</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>啟用</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>停用</option>
                    </select>
                </div>
            </div>
        </details>
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">員工編號</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">姓名</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">參與專案</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">角色</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">帳號開啟日</th>
                <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-3 py-2 whitespace-nowrap text-sm text-center space-x-2">
                    @can('users.edit')
                    <a href="{{ route('tenant.users.edit', $user) }}" 
                       class="text-primary hover:text-primary-dark font-medium">
                        編輯
                    </a>
                    @endcan
                    @can('users.edit')
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('tenant.users.destroy', $user) }}" class="inline"
                          onsubmit="return confirm('確定刪除「{{ addslashes($user->name) }}」？此操作無法復原。')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium">刪除</button>
                    </form>
                    @endif
                    @endcan
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $user->employee_no ?? '-' }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $user->name }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->email }}
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
                    @php
                        $userRole = $user->roles->first();
                    @endphp
                    @if($userRole)
                        @if($userRole->name === 'Admin')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                系統管理員
                            </span>
                        @elseif($userRole->name === 'Manager')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                總管理/主管
                            </span>
                        @elseif($userRole->name === '會計人員')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                會計
                            </span>
                        @elseif($userRole->name === 'Member')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                成員
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                {{ $userRole->name }}
                            </span>
                        @endif
                    @else
                        <span class="text-gray-400 dark:text-gray-500">未設定</span>
                    @endif
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    @date($user->created_at)
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
                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
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
