@extends('layouts.tenant')

@section('title', '新增稅率')

@section('content')
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        財務設定 &gt; <a href="{{ route('tenant.tax-settings.index') }}" class="text-primary hover:underline">稅款設定</a> &gt; 新增
    </p>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">新增稅率設定</h1>
</div>

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
    <form action="{{ route('tenant.tax-settings.store') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                名稱 <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 @error('name') border-red-500 @enderror"
                   placeholder="例如：營業稅、進項稅" required>
            @error('name')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                稅率（%） <span class="text-red-500">*</span>
            </label>
            <input type="number" name="rate" value="{{ old('rate') }}" step="0.01" min="0" max="100"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 @error('rate') border-red-500 @enderror"
                   placeholder="例如：5" required>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">輸入百分比數值，如 5 代表 5%</p>
            @error('rate')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">說明</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">{{ old('description') }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">排序</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
        </div>

        <div class="mb-6 space-y-3">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-4 h-4 text-primary border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">啟用此稅率</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                       class="w-4 h-4 text-primary border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">設為預設稅率</span>
            </label>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('tenant.tax-settings.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded-lg">取消</a>
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">新增</button>
        </div>
    </form>
</div>
@endsection
