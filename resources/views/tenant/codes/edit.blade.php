@extends('layouts.tenant')

@section('title', '編輯' . $categoryName)

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            編輯{{ $categoryName }}
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li><a class="font-medium" href="{{ route('tenant.dashboard') }}">儀表板 /</a></li>
                <li><a class="font-medium" href="{{ route('tenant.codes.index') }}">代碼管理 /</a></li>
                <li><a class="font-medium" href="{{ route('tenant.codes.category', $category) }}">{{ $categoryName }} /</a></li>
                <li class="font-medium text-primary">編輯</li>
            </ol>
        </nav>
    </div>

    <!-- Form -->
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white">代碼資料</h3>
        </div>
        
        <form method="POST" action="{{ route('tenant.codes.update', [$category, $code]) }}">
            @csrf
            @method('PUT')
            <div class="p-6.5">
                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                    <!-- Code -->
                    <div class="w-full xl:w-1/2">
                        <label class="mb-2.5 block text-black dark:text-white">
                            代碼 <span class="text-meta-1">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', $code->code) }}" required
                               class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary @error('code') border-meta-1 @enderror">
                        @error('code')
                            <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div class="w-full xl:w-1/2">
                        <label class="mb-2.5 block text-black dark:text-white">
                            名稱 <span class="text-meta-1">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $code->name) }}" required
                               class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary @error('name') border-meta-1 @enderror">
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
                    <input type="number" name="sort_order" value="{{ old('sort_order', $code->sort_order) }}" min="0" required
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
                              class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">{{ old('description', $code->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="mb-6">
                    <label class="flex cursor-pointer select-none items-center">
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $code->is_active) ? 'checked' : '' }}
                                   class="sr-only" onchange="this.nextElementSibling.classList.toggle('bg-primary')">
                            <div class="block h-8 w-14 rounded-full {{ old('is_active', $code->is_active) ? 'bg-primary' : 'bg-meta-9 dark:bg-[#5A616B]' }}"></div>
                            <div class="dot absolute left-1 top-1 h-6 w-6 rounded-full bg-white transition {{ old('is_active', $code->is_active) ? 'translate-x-full' : '' }}"></div>
                        </div>
                        <div class="ml-3 text-sm font-medium text-black dark:text-white">
                            啟用此代碼
                        </div>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="submit" class="flex justify-center rounded bg-primary px-6 py-2 font-medium text-gray hover:shadow-1">
                        更新
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
