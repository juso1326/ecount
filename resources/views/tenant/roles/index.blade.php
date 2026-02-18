@extends('layouts.tenant')

@section('title', '角色權限管理')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">角色權限管理</h1>
        <a href="{{ route('tenant.roles.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            新增角色
        </a>
    </div>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        角色名稱
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        權限數量
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        用戶數量
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        類型
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        操作
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($roles as $role)
                @php
                    $isSystem = in_array($role->name, ['總管理', '財務主管', '專案經理', '會計人員', '一般員工']);
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $role->name }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $role->permissions_count }} 個權限
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $role->users_count }} 位用戶
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('tenant.roles.show', $role) }}" 
                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 mr-3">
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
