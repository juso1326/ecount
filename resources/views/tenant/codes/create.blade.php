@extends('layouts.tenant')

@section('title', '新增' . $categoryName)

@section('page-title', '新增' . $categoryName)

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">專案帳戶管理 &gt; 代碼管理 &gt; {{ $categoryName }} &gt; 新增</p>
</div>

<!-- 頁面標題 -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">新增{{ $categoryName }}</h1>
</div>

<!-- 表單 -->
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <form method="POST" action="{{ route('tenant.codes.store', $category) }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 代碼 -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    代碼 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror">
                @error('code')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 名稱 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    名稱 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
                        @error('name')
                            <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sort Order -->
                <div class="mb-4.5">
                    <label class="mb-2.5 block text-black dark:text-white">
                        排序 <span class="text-meta-1">*</span>
                    </label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $nextSortOrder) }}" min="0" required
                           class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                    @error('sort_order')
                        <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-body">數字越小排序越前面</p>
                </div>

                <!-- Description -->
                <div class="mb-4.5">
                    <label class="mb-2.5 block text-black dark:text-white">說明</label>
                    <textarea name="description" rows="3"
                              class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="mb-6">
                    <label class="flex cursor-pointer select-none items-center">
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="sr-only" onchange="this.nextElementSibling.classList.toggle('bg-primary')">
                            <div class="block h-8 w-14 rounded-full bg-meta-9 dark:bg-[#5A616B]"></div>
                            <div class="dot absolute left-1 top-1 h-6 w-6 rounded-full bg-white transition"></div>
                        </div>
                        <div class="ml-3 text-sm font-medium text-black dark:text-white">
                            啟用此代碼
                        </div>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="submit" class="flex justify-center rounded bg-primary px-6 py-2 font-medium text-gray hover:shadow-1">
                        儲存
                    </button>
                    <a href="{{ route('tenant.codes.category', $category) }}" 
                       class="flex justify-center rounded border border-stroke px-6 py-2 font-medium text-black hover:shadow-1 dark:border-strokedark dark:text-white">
                        取消
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
input:checked ~ .dot {
    transform: translateX(100%);
}
</style>
@endsection
