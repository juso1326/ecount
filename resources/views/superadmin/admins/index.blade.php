@extends('layouts.superadmin')

@section('title', '帳號管理')
@section('page-title', '帳號管理')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div><!-- spacer --></div>
        <a href="{{ route('superadmin.admins.create') }}"
           class="inline-flex items-center px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            新增帳號
        </a>
    </div>

    <!-- Search -->
    <form method="GET" id="search-form" class="flex flex-wrap gap-2">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                   placeholder="搜尋名稱、Email…"
                   class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-white">
            <button type="button" id="clear-search"
                    class="{{ request('search') ? '' : 'hidden' }} absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    onclick="document.getElementById('search-input').value='';document.getElementById('search-form').submit()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <select name="status" onchange="document.getElementById('search-form').submit()"
                class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary">
            <option value="">所有狀態</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>啟用</option>
            <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>停用</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>已停權</option>
        </select>
    </form>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">名稱</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">最後登入</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($admins as $admin)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition {{ $admin->trashed() ? 'opacity-60' : '' }}">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-primary flex items-center justify-center text-white text-xs font-medium">
                                {{ mb_substr($admin->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $admin->name }}
                                @if($admin->id === $currentAdmin->id)
                                    <span class="ml-1 text-xs text-gray-400">(我)</span>
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $admin->email }}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-center">
                        @if($admin->trashed())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1"></span>已停權
                            </span>
                        @elseif($admin->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>啟用
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></span>停用
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : '從未登入' }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-center text-sm">
                        <div class="flex items-center justify-center gap-2">
                            @if(!$admin->trashed())
                                <a href="{{ route('superadmin.admins.edit', $admin) }}"
                                   class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-xs font-medium">編輯</a>
                                @if($admin->id !== $currentAdmin->id)
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <form action="{{ route('superadmin.admins.suspend', $admin) }}" method="POST" class="inline"
                                          onsubmit="return confirm('確定要停權「{{ $admin->name }}」嗎？')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-medium">停權</button>
                                    </form>
                                @endif
                            @else
                                <form action="{{ route('superadmin.admins.restore', $admin->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 text-xs font-medium">恢復</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                        找不到符合的帳號
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($admins->hasPages())
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3">
            {{ $admins->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    let debounce;
    document.getElementById('search-input').addEventListener('input', function () {
        const clearBtn = document.getElementById('clear-search');
        clearBtn.classList.toggle('hidden', !this.value);
        clearTimeout(debounce);
        debounce = setTimeout(() => document.getElementById('search-form').submit(), 500);
    });
</script>
@endpush
@endsection
