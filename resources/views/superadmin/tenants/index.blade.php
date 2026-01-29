@extends('layouts.superadmin')

@section('title', '租戶管理')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">租戶管理</h1>
    <a href="{{ route('superadmin.tenants.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
        + 新增租戶
    </a>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="{{ route('superadmin.tenants.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="搜尋租戶 ID、名稱、Email..." class="border rounded px-3 py-2">
        
        <select name="status" class="border rounded px-3 py-2">
            <option value="">全部狀態</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>啟用中</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>已暫停</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>未啟用</option>
        </select>
        
        <select name="plan" class="border rounded px-3 py-2">
            <option value="">全部方案</option>
            <option value="basic" {{ request('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
            <option value="professional" {{ request('plan') === 'professional' ? 'selected' : '' }}>Professional</option>
            <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
        </select>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex-1">
                搜尋
            </button>
            
            @if(request()->hasAny(['search', 'status', 'plan']))
                <a href="{{ route('superadmin.tenants.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    清除
                </a>
            @endif
        </div>
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">租戶 ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">名稱</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">方案</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">建立時間</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($tenants as $tenant)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tenant->id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tenant->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tenant->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full
                        @if($tenant->plan === 'basic') bg-blue-100 text-blue-800
                        @elseif($tenant->plan === 'professional') bg-indigo-100 text-indigo-800
                        @else bg-purple-100 text-purple-800
                        @endif">
                        {{ ucfirst($tenant->plan) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($tenant->status === 'active')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            啟用中
                        </span>
                    @elseif($tenant->status === 'suspended')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            已暫停
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            未啟用
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $tenant->created_at->format('Y-m-d H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="text-blue-600 hover:text-blue-900">查看</a>
                    <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="text-indigo-600 hover:text-indigo-900">編輯</a>
                    
                    @if($tenant->status === 'active')
                        <form action="{{ route('superadmin.tenants.suspend', $tenant) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-600 hover:text-yellow-900" onclick="return confirm('確定要暫停此租戶嗎？')">暫停</button>
                        </form>
                    @elseif($tenant->status === 'suspended')
                        <form action="{{ route('superadmin.tenants.activate', $tenant) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900">啟用</button>
                        </form>
                    @endif
                    
                    <form action="{{ route('superadmin.tenants.destroy', $tenant) }}" method="POST" class="inline" onsubmit="return confirm('⚠️ 警告：刪除租戶將永久刪除所有資料！\n\n確定要刪除租戶【{{ $tenant->name }}】嗎？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">刪除</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    沒有找到任何租戶
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 分頁 -->
<div class="mt-4">
    {{ $tenants->links() }}
</div>
@endsection
