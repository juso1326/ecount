@extends('layouts.tenant')

@section('title', '角色詳情')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
        <a href="{{ route('tenant.roles.index') }}" class="hover:text-primary">角色權限管理</a>
        <span>/</span>
        <span>角色詳情</span>
    </div>
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $role->name }}</h1>
        @if(!in_array($role->name, ['總管理', '財務主管', '專案經理']))
        <a href="{{ route('tenant.roles.edit', $role) }}" 
           class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition">
            編輯角色
        </a>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">權限列表</h2>
            
            @php
                $groupedPermissions = $role->permissions->groupBy(function($permission) {
                    return explode('.', $permission->name)[0];
                });
            @endphp
            
            <div class="space-y-4">
                @foreach($groupedPermissions as $module => $permissions)
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                        {{ \App\Helpers\PermissionHelper::getModuleName($module) }}
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($permissions as $permission)
                        <span class="px-3 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 rounded-full">
                            {{ \App\Helpers\PermissionHelper::getActionName(explode('.', $permission->name)[1]) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div>
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">統計資訊</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">權限數量</dt>
                    <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $role->permissions->count() }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">用戶數量</dt>
                    <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $role->users->count() }}</dd>
                </div>
            </dl>
        </div>

        @if($role->users->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">使用此角色的用戶</h2>
            <ul class="space-y-2">
                @foreach($role->users->take(5) as $user)
                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    {{ $user->name }}
                </li>
                @endforeach
                @if($role->users->count() > 5)
                <li class="text-sm text-gray-500 dark:text-gray-400">
                    還有 {{ $role->users->count() - 5 }} 位用戶...
                </li>
                @endif
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection
