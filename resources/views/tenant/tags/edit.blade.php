@extends('layouts.tenant')

@section('title', '編輯標籤')

@section('page-title', '編輯標籤')

@section('content')
<!-- 頁面標題 -->
<div class="mb-3">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">編輯標籤</h1>
</div>

<!-- 表單 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
    <form action="{{ route('tenant.tags.update', $tag) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- 標籤類型 -->
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                標籤類型 <span class="text-red-500">*</span>
            </label>
            <select name="type" 
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent @error('type') border-red-500 @enderror"
                    required>
                <option value="">請選擇類型</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ old('type', $tag->type) === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- 標籤名稱 -->
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                標籤名稱 <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="name" 
                   value="{{ old('name', $tag->name) }}"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror"
                   required>
            @error('name')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- 標籤顏色 -->
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                標籤顏色 <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center space-x-4">
                <input type="color" 
                       name="color" 
                       value="{{ old('color', $tag->color) }}"
                       class="w-20 h-10 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                <input type="text" 
                       name="color_text" 
                       value="{{ old('color', $tag->color) }}"
                       class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 @error('color') border-red-500 @enderror"
                       pattern="^#[0-9A-Fa-f]{6}$">
            </div>
            @error('color')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- 說明 -->
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                說明
            </label>
            <textarea name="description" 
                      rows="3"
                      class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description', $tag->description) }}</textarea>
        </div>

        <!-- 排序 -->
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                排序
            </label>
            <input type="number" 
                   name="sort_order" 
                   value="{{ old('sort_order', $tag->sort_order) }}"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                   min="0">
        </div>

        <!-- 是否啟用 -->
        <div class="mb-3">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="is_active" 
                       value="1"
                       {{ old('is_active', $tag->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">啟用此標籤</span>
            </label>
        </div>

        <!-- 按鈕 -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('tenant.tags.index', ['type' => $tag->type]) }}" 
               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                取消
            </a>
            <button type="submit" 
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
                更新
            </button>
        </div>
    </form>
</div>

<script>
// 同步顏色選擇器和文字輸入
document.querySelector('input[type="color"]').addEventListener('input', function(e) {
    document.querySelector('input[name="color_text"]').value = e.target.value;
});

document.querySelector('input[name="color_text"]').addEventListener('input', function(e) {
    if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
        document.querySelector('input[type="color"]').value = e.target.value;
    }
});

// 表單提交時確保使用正確的值
document.querySelector('form').addEventListener('submit', function(e) {
    const colorText = document.querySelector('input[name="color_text"]').value;
    document.querySelector('input[name="color"]').value = colorText;
});
</script>
@endsection
