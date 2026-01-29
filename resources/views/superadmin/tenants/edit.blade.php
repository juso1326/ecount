@extends('layouts.superadmin')

@section('title', '編輯租戶')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">編輯租戶</h1>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 租戶 ID（不可編輯） -->
            <div>
                <label for="id" class="block text-sm font-medium text-gray-700">租戶 ID</label>
                <input type="text" id="id" value="{{ $tenant->id }}" disabled
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 bg-gray-100 text-gray-500">
                <p class="mt-1 text-sm text-gray-500">租戶 ID 無法修改</p>
            </div>

            <!-- 租戶名稱 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">租戶名稱 <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $tenant->name) }}" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $tenant->email) }}" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 方案 -->
            <div>
                <label for="plan" class="block text-sm font-medium text-gray-700">方案 <span class="text-red-500">*</span></label>
                <select name="plan" id="plan" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('plan') border-red-500 @enderror">
                    <option value="basic" {{ old('plan', $tenant->plan) === 'basic' ? 'selected' : '' }}>Basic - 基礎方案</option>
                    <option value="professional" {{ old('plan', $tenant->plan) === 'professional' ? 'selected' : '' }}>Professional - 專業方案</option>
                    <option value="enterprise" {{ old('plan', $tenant->plan) === 'enterprise' ? 'selected' : '' }}>Enterprise - 企業方案</option>
                </select>
                @error('plan')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 狀態 -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">狀態 <span class="text-red-500">*</span></label>
                <select name="status" id="status" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                    <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>啟用中</option>
                    <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>已暫停</option>
                    <option value="inactive" {{ old('status', $tenant->status) === 'inactive' ? 'selected' : '' }}>未啟用</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 資料庫名稱（只讀） -->
            <div>
                <label for="database" class="block text-sm font-medium text-gray-700">資料庫名稱</label>
                <input type="text" id="database" value="{{ $tenant->getDatabaseName() }}" disabled
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 bg-gray-100 text-gray-500 font-mono">
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                取消
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                更新
            </button>
        </div>
    </form>
</div>
@endsection
