@extends('layouts.tenant')

@section('title', '使用者管理')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">使用者管理</h1>
    <a href="{{ route('tenant.users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        + 新增使用者
    </a>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="{{ route('tenant.users.index') }}" class="flex gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="搜尋姓名、Email..." class="flex-1 border rounded px-3 py-2">
        
        <select name="is_active" class="border rounded px-3 py-2">
            <option value="">全部狀態</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>啟用</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>停用</option>
        </select>
        
        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            搜尋
        </button>
        
        @if(request()->hasAny(['search', 'is_active']))
            <a href="{{ route('tenant.users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                清除
            </a>
        @endif
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">姓名</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">最後登入</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">建立時間</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $user->name }}
                    @if($user->id === auth()->id())
                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">你</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($user->is_active)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            啟用
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            停用
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $user->created_at->format('Y-m-d H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('tenant.users.show', $user) }}" class="text-blue-600 hover:text-blue-900 mr-3">查看</a>
                    <a href="{{ route('tenant.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">編輯</a>
                    
                    @if($user->id !== auth()->id())
                        <form action="{{ route('tenant.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('確定要刪除此使用者嗎？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">刪除</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    沒有找到任何使用者
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 分頁 -->
<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
