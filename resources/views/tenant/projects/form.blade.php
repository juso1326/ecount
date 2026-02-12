@extends('layouts.tenant')

@section('title', isset($project) ? '編輯專案' : '新增專案')

@section('content')
<div class="mb-3">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ isset($project) ? '編輯專案' : '新增專案' }}</h1>
</div>

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <form method="POST" action="{{ isset($project) ? route('tenant.projects.update', $project) : route('tenant.projects.store') }}">
        @csrf
        @if(isset($project))
            @method('PUT')
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <!-- 專案代碼 -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">專案代碼 <span class="text-gray-500">(自動)</span></label>
                <input type="text" name="code" id="code" value="{{ $nextCode ?? $project->code ?? '' }}" readonly
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 cursor-not-allowed">
                @if(!isset($project))
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">系統將自動產生專案代碼</p>
                @endif
            </div>

            <!-- 開案日期 -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">開案日期</label>
                <input type="date" name="start_date" id="start_date" 
                    value="{{ old('start_date', isset($project) ? $project->start_date?->format('Y-m-d') : date('Y-m-d')) }}"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror">
                @error('start_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 專案名稱 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">專案名稱 <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $project->name ?? '') }}" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 客戶(3W公司) -->
            <div>
                <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">客戶 <span class="text-red-500">*</span></label>
                <select name="company_id" id="company_id" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('company_id') border-red-500 @enderror">
                    <option value="">請選擇</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $project->company_id ?? '') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 報價單號 -->
            <div>
                <label for="quote_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300">報價單號</label>
                <input type="text" name="quote_no" id="quote_no" value="{{ old('quote_no', $project->quote_no ?? '') }}"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 總額(含稅) -->
            <div>
                <label for="budget" class="block text-sm font-medium text-gray-700 dark:text-gray-300">總額</label>
                <input type="number" name="budget" id="budget" value="{{ old('budget', $project->budget ?? 0) }}" step="0.01" min="0"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">請填寫含稅總額</p>
            </div>

            <!-- 專案負責人 -->
            <div>
                <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">專案負責人</label>
                <select name="manager_id" id="manager_id"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">請選擇</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ old('manager_id', $project->manager_id ?? auth()->id()) == $manager->id ? 'selected' : '' }}>
                            {{ $manager->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('tenant.projects.index') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-bold py-2 px-4 rounded">
                取消
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ isset($project) ? '更新' : '新增' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // 客戶選擇 Select2
    $('#company_id').select2({
        placeholder: '請選擇客戶',
        allowClear: false,
        width: '100%'
    });

    // 專案負責人 - 可搜尋
    $('#manager_id').select2({
        placeholder: '請選擇專案負責人',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
@endsection
