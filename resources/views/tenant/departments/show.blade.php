@extends('layouts.tenant')

@section('title', '部門詳情')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">部門詳情</h1>
    <div class="space-x-2">
        <a href="{{ route('tenant.departments.edit', $department) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            編輯
        </a>
        <a href="{{ route('tenant.departments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
            返回列表
        </a>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-500">部門代碼</label>
            <p class="mt-1 text-lg text-gray-900">{{ $department->code }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">部門名稱</label>
            <p class="mt-1 text-lg text-gray-900">{{ $department->name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">上層部門</label>
            <p class="mt-1 text-lg text-gray-900">{{ $department->parent?->name ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">部門主管</label>
            <p class="mt-1 text-lg text-gray-900">{{ $department->manager?->name ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">完整路徑</label>
            <p class="mt-1 text-lg text-gray-900">{{ $department->full_path }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">排序順序</label>
            <p class="mt-1 text-lg text-gray-900">{{ $department->sort_order }}</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-500">描述</label>
            <p class="mt-1 text-lg text-gray-900">{{ $department->description ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">狀態</label>
            <p class="mt-1">
                @if($department->is_active)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        啟用
                    </span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        停用
                    </span>
                @endif
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">建立時間</label>
            <p class="mt-1 text-lg text-gray-900">{{ $department->created_at->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</div>

<!-- 下層部門 -->
@if($department->children->count() > 0)
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">下層部門</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($department->children as $child)
            <div class="border rounded p-4">
                <p class="font-medium">{{ $child->name }}</p>
                <p class="text-sm text-gray-500">{{ $child->code }}</p>
                <a href="{{ route('tenant.departments.show', $child) }}" class="text-blue-600 hover:text-blue-900 text-sm">查看詳情</a>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- 相關專案 -->
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">相關專案</h2>
    
    @if($department->projects->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">專案代碼</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">專案名稱</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">狀態</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">預算</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($department->projects as $project)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $project->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $project->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">{{ $project->status }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($project->budget) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('tenant.projects.show', $project) }}" class="text-blue-600 hover:text-blue-900">查看</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">尚無相關專案</p>
    @endif
</div>
@endsection
