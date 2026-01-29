@extends('layouts.superadmin')

@section('title', '新增租戶')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">新增租戶</h1>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <form method="POST" action="{{ route('superadmin.tenants.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 租戶 ID -->
            <div>
                <label for="id" class="block text-sm font-medium text-gray-700">租戶 ID <span class="text-red-500">*</span></label>
                <input type="text" name="id" id="id" value="{{ old('id') }}" required
                    placeholder="例如：abc123（僅限小寫字母和數字）"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('id') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">此 ID 將作為子域名和資料庫名稱</p>
                @error('id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 租戶名稱 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">租戶名稱 <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    placeholder="例如：阿福科技股份有限公司"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    placeholder="admin@example.com"
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
                    <option value="">請選擇方案</option>
                    <option value="basic" {{ old('plan') === 'basic' ? 'selected' : '' }}>Basic - 基礎方案</option>
                    <option value="professional" {{ old('plan') === 'professional' ? 'selected' : '' }}>Professional - 專業方案</option>
                    <option value="enterprise" {{ old('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise - 企業方案</option>
                </select>
                @error('plan')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 自訂域名（選填） -->
            <div class="md:col-span-2">
                <label for="domain" class="block text-sm font-medium text-gray-700">自訂域名（選填）</label>
                <input type="text" name="domain" id="domain" value="{{ old('domain') }}"
                    placeholder="例如：custom.example.com"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                <p class="mt-1 text-sm text-gray-500">留空則使用預設子域名：[租戶ID].localhost</p>
            </div>
        </div>

        <!-- 警告提示 -->
        <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>注意事項：</strong>
                    </p>
                    <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside">
                        <li>系統將自動建立獨立資料庫：tenant_[租戶ID]_db</li>
                        <li>自動執行資料庫遷移並建立管理員帳號（email/password: admin@[租戶ID].com / password）</li>
                        <li>租戶 ID 建立後無法修改</li>
                        <li>建立過程可能需要幾秒鐘時間</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('superadmin.tenants.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                取消
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                建立租戶
            </button>
        </div>
    </form>
</div>
@endsection
