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
                <input type="text" name="id" id="id" value="{{ old('id') }}"
                    data-rules="required" data-label="租戶 ID"
                    placeholder="例如：abc123（僅限小寫字母和數字）"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('id') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">此 ID 將作為子域名和資料庫名稱，建立後無法修改</p>
                @error('id')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <!-- 租戶名稱 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">公司名稱 <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    data-rules="required" data-label="公司名稱"
                    placeholder="例如：阿福科技股份有限公司"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                @error('name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">管理員 Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    data-rules="required|email" data-label="Email"
                    placeholder="admin@example.com"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">系統將自動產生初始密碼並以此信箱為帳號</p>
                @error('email')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <!-- 自訂域名（選填） -->
            <div>
                <label for="domain" class="block text-sm font-medium text-gray-700">自訂域名 <span class="text-gray-400 font-normal">（選填）</span></label>
                <input type="text" name="domain" id="domain" value="{{ old('domain') }}"
                    placeholder="留空使用預設：[租戶ID].{{ config('app.domain') }}"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <!-- 租用方案 -->
        <div class="mt-8">
            <label class="block text-sm font-medium text-gray-700 mb-3">租用方案 <span class="text-red-500">*</span></label>
            @error('plan')<p class="mb-2 text-sm text-red-500">{{ $message }}</p>@enderror

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($plans as $plan)
                @php
                    $selected = old('plan') === $plan->slug;
                    $featured = $plan->is_featured;
                @endphp
                <label class="relative cursor-pointer">
                    <input type="radio" name="plan" value="{{ $plan->slug }}"
                        class="sr-only peer" {{ $selected ? 'checked' : '' }} required>
                    <div class="border-2 rounded-lg p-4 transition-all
                        peer-checked:border-indigo-600 peer-checked:bg-indigo-50
                        hover:border-gray-400
                        {{ $featured ? 'border-indigo-300' : 'border-gray-200' }}">

                        @if($featured)
                        <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-xs font-semibold px-3 py-0.5 rounded-full">推薦</span>
                        @endif

                        <div class="font-semibold text-gray-900">{{ $plan->name }}</div>
                        <div class="mt-1 text-2xl font-bold text-indigo-600">
                            NT${{ number_format($plan->price) }}
                            <span class="text-sm font-normal text-gray-500">/ 月</span>
                        </div>
                        @if($plan->annual_price)
                        <div class="text-xs text-green-600 mt-0.5">年繳 NT${{ number_format($plan->annual_price) }}（省 {{ round((1 - $plan->annual_price / ($plan->price * 12)) * 100) }}%）</div>
                        @endif

                        <div class="mt-3 space-y-1 text-sm text-gray-600">
                            <div>👥 {{ $plan->max_users ? $plan->max_users.'人' : '不限人數' }}</div>
                            <div>🏢 {{ $plan->max_companies ? $plan->max_companies.'間公司' : '不限公司' }}</div>
                            <div>📁 {{ $plan->max_projects ? $plan->max_projects.'個專案' : '不限專案' }}</div>
                            <div>💾 {{ $plan->storage_limit ? number_format($plan->storage_limit / 1024, 0).' GB' : '不限空間' }}</div>
                        </div>

                        @if($plan->features)
                        <ul class="mt-3 space-y-1 text-xs text-gray-500 border-t pt-3">
                            @foreach(array_slice($plan->features, 0, 5) as $feature)
                            <li class="flex items-center gap-1"><span class="text-green-500">✓</span> {{ $feature }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <!-- 警告提示 -->
        <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <p class="text-sm text-yellow-800 font-medium">注意事項</p>
            <ul class="mt-1 text-sm text-yellow-700 list-disc list-inside space-y-1">
                <li>系統將自動建立獨立資料庫 <code class="bg-yellow-100 px-1 rounded">tenant_[租戶ID]_db</code></li>
                <li>自動產生隨機初始密碼，請通知管理員修改</li>
                <li>租戶 ID 建立後無法修改</li>
                <li>建立過程約需 3-10 秒</li>
            </ul>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('superadmin.tenants.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-5 rounded-lg transition">取消</a>
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-5 rounded-lg transition">
                建立租戶
            </button>
        </div>
    </form>
</div>
@endsection
