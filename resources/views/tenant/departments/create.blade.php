@extends('layouts.tenant')

@section('title', '新增部門')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">新增部門</h1>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <form method="POST" action="{{ route('tenant.departments.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 部門代碼 -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700">部門代碼 <span class="text-red-500">*</span></label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror">
                @error('code')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 部門名稱 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">部門名稱 <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 上層部門 -->
            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700">上層部門</label>
                <select name="parent_id" id="parent_id"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">無（頂層部門）</option>
                    @foreach($parentDepartments as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 主管 -->
            <div>
                <label for="manager_id" class="block text-sm font-medium text-gray-700">部門主管</label>
                <select name="manager_id" id="manager_id"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">請選擇</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                            {{ $manager->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 排序 -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700">排序順序</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 描述 -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">描述</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
            </div>

            <!-- 狀態 -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">啟用</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('tenant.departments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                取消
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                新增
            </button>
        </div>
    </form>
</div>
@endsection
