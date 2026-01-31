@extends('layouts.tenant')

@section('title', '編輯專案')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">編輯專案</h1>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <form method="POST" action="{{ route('tenant.projects.update', $project) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 專案代碼 -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700">專案代碼 <span class="text-red-500">*</span></label>
                <input type="text" name="code" id="code" value="{{ old('code', $project->code) }}" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror">
                @error('code')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 專案名稱 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">專案名稱 <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 所屬公司 -->
            <div>
                <label for="company_id" class="block text-sm font-medium text-gray-700">所屬公司 <span class="text-red-500">*</span></label>
                <select name="company_id" id="company_id" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('company_id') border-red-500 @enderror">
                    <option value="">請選擇</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $project->company_id) == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 所屬部門 -->
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700">所屬部門</label>
                <select name="department_id" id="department_id"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">請選擇</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id', $project->department_id) == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 專案經理 -->
            <div>
                <label for="manager_id" class="block text-sm font-medium text-gray-700">專案經理</label>
                <select name="manager_id" id="manager_id"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 select2">
                    <option value="">請選擇</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ old('manager_id', $project->manager_id) == $manager->id ? 'selected' : '' }}>
                            {{ $manager->name }} ({{ $manager->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 狀態 -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">專案狀態 <span class="text-red-500">*</span></label>
                <select name="status" id="status" required
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="planning" {{ old('status', $project->status) === 'planning' ? 'selected' : '' }}>規劃中</option>
                    <option value="in_progress" {{ old('status', $project->status) === 'in_progress' ? 'selected' : '' }}>進行中</option>
                    <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>暫停</option>
                    <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>已完成</option>
                    <option value="cancelled" {{ old('status', $project->status) === 'cancelled' ? 'selected' : '' }}>已取消</option>
                </select>
            </div>

            <!-- 開始日期 -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">開始日期</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 結束日期 -->
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">結束日期</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror">
                @error('end_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 預算 -->
            <div>
                <label for="budget" class="block text-sm font-medium text-gray-700">預算金額</label>
                <input type="number" name="budget" id="budget" value="{{ old('budget', $project->budget) }}" step="0.01" min="0"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 實際成本 -->
            <div>
                <label for="actual_cost" class="block text-sm font-medium text-gray-700">實際成本</label>
                <input type="number" name="actual_cost" id="actual_cost" value="{{ old('actual_cost', $project->actual_cost) }}" step="0.01" min="0"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 描述 -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">專案描述</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $project->description) }}</textarea>
            </div>

            <!-- 備註 -->
            <div class="md:col-span-2">
                <label for="note" class="block text-sm font-medium text-gray-700">備註</label>
                <textarea name="note" id="note" rows="2"
                    class="mt-1 block w-full border rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('note', $project->note) }}</textarea>
            </div>

            <!-- 狀態 -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $project->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">啟用</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('tenant.projects.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                取消
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                更新
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
    $('#manager_id').select2({
        placeholder: '搜尋專案經理...',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
@endsection
