@extends('layouts.tenant')

@section('title', '角色權限管理')

@section('page-title', '角色權限管理')

@section('content')
<div class="mb-2 flex justify-end items-center">
    <a href="{{ route('tenant.roles.create') }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增角色
    </a>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" action="{{ route('tenant.roles.index') }}" class="space-y-4">
        <!-- 智能搜尋框 -->
        <div class="flex gap-2">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="🔍 智能搜尋：角色名稱..." 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-transparent text-base">
            </div>
            <button type="submit" 
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                搜尋
            </button>
            @if(request('search'))
                <a href="{{ route('tenant.roles.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                    清除
                </a>
            @endif
        </div>
    </form>
</div>

@if(session('success'))
<div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg">
    {{ session('error') }}
</div>
@endif

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">詳細</th>
                    <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">編輯</th>
                    <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">角色名稱</th>
                    <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">權限數量</th>
                    <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">用戶數量</th>
                    <th class="px-6 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">類型</th>
                    <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">刪除</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($roles as $role)
                @php
                    $isSystem = in_array($role->name, ['總管理', '財務主管', '專案經理']);
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-center">
                        <a href="{{ route('tenant.roles.show', $role) }}" 
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                            詳細
                        </a>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-center">
                        @if(!$isSystem)
                        <a href="{{ route('tenant.roles.edit', $role) }}" 
                           class="text-primary hover:text-primary-dark font-medium">
                            編輯
                        </a>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $role->name }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $role->permissions_count }} 個權限
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $role->users_count }} 位用戶
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap">
                        @if($isSystem)
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 rounded">
                            系統預設
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded">
                            自訂角色
                        </span>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-center">
                        @if(!$isSystem)
                        <form action="{{ route('tenant.roles.destroy', $role) }}" method="POST" class="inline"
                              onsubmit="return confirm('確定要刪除「{{ $role->name }}」角色嗎？\n\n注意：該角色下的 {{ $role->users_count }} 位用戶將失去此角色權限。');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 font-medium">
                                刪除
                            </button>
                        </form>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                            檢視
                        </a>
                        @if(!$isSystem)
                        <a href="{{ route('tenant.roles.edit', $role) }}" 
                           class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mr-3">
                            編輯
                        </a>
                        @if($role->users_count == 0)
                        <form action="{{ route('tenant.roles.destroy', $role) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('確定要刪除此角色嗎？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                刪除
                            </button>
                        </form>
                        @endif
                        @else
                        <span class="text-gray-400 dark:text-gray-600">系統保護</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        尚無角色資料
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
    <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">💡 說明</h3>
    <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
        <li>• 系統預設角色無法刪除或修改權限，確保系統穩定性</li>
        <li>• 可以建立自訂角色並靈活配置權限</li>
        <li>• 已分配給用戶的角色無法刪除</li>
    </ul>
</div>
@endsection
