@extends('layouts.superadmin')

@section('title', '租戶詳情')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">租戶詳情</h1>
    <div class="space-x-2">
        <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            編輯
        </a>
        <a href="{{ route('superadmin.tenants.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
            返回列表
        </a>
    </div>
</div>

<!-- 基本資訊 -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">基本資訊</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-500">租戶 ID</label>
            <p class="mt-1 text-lg text-gray-900">{{ $tenant->id }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">租戶名稱</label>
            <p class="mt-1 text-lg text-gray-900">{{ $tenant->name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">Email</label>
            <p class="mt-1 text-lg text-gray-900">{{ $tenant->email }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">方案</label>
            <p class="mt-1">
                <span class="px-2 py-1 text-sm rounded-full
                    @if($tenant->plan === 'basic') bg-blue-100 text-blue-800
                    @elseif($tenant->plan === 'professional') bg-indigo-100 text-indigo-800
                    @else bg-purple-100 text-purple-800
                    @endif">
                    {{ ucfirst($tenant->plan) }}
                </span>
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">狀態</label>
            <p class="mt-1">
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
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">資料庫名稱</label>
            <p class="mt-1 text-lg text-gray-900 font-mono">{{ $tenant->getDatabaseName() }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">建立時間</label>
            <p class="mt-1 text-lg text-gray-900">{{ $tenant->created_at->format('Y-m-d H:i:s') }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">更新時間</label>
            <p class="mt-1 text-lg text-gray-900">{{ $tenant->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</div>

<!-- 域名資訊 -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">域名資訊</h2>
    <div class="space-y-2">
        @forelse($tenant->domains as $domain)
            <div class="flex items-center justify-between border-b pb-2">
                <span class="font-mono text-gray-900">{{ $domain->domain }}</span>
                <a href="http://{{ $domain->domain }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">
                    訪問 →
                </a>
            </div>
        @empty
            <p class="text-gray-500">尚無域名</p>
        @endforelse
    </div>
</div>

<!-- 資料統計 -->
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">資料統計</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="text-center">
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['companies'] }}</p>
            <p class="text-sm text-gray-500 mt-1">公司數量</p>
        </div>
        <div class="text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stats['departments'] }}</p>
            <p class="text-sm text-gray-500 mt-1">部門數量</p>
        </div>
        <div class="text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $stats['projects'] }}</p>
            <p class="text-sm text-gray-500 mt-1">專案數量</p>
        </div>
        <div class="text-center">
            <p class="text-3xl font-bold text-purple-600">{{ $stats['users'] }}</p>
            <p class="text-sm text-gray-500 mt-1">使用者數量</p>
        </div>
    </div>
</div>

<!-- 操作按鈕 -->
<div class="mt-6 flex justify-end space-x-3">
    @if($tenant->status === 'active')
        <form action="{{ route('superadmin.tenants.suspend', $tenant) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded" onclick="return confirm('確定要暫停此租戶嗎？')">
                暫停租戶
            </button>
        </form>
    @elseif($tenant->status === 'suspended')
        <form action="{{ route('superadmin.tenants.activate', $tenant) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                啟用租戶
            </button>
        </form>
    @endif
    
    <form action="{{ route('superadmin.tenants.destroy', $tenant) }}" method="POST" class="inline" onsubmit="return confirm('⚠️ 警告：刪除租戶將永久刪除所有資料！\n\n確定要刪除租戶【{{ $tenant->name }}】嗎？');">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
            刪除租戶
        </button>
    </form>
</div>
@endsection
