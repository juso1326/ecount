@extends('layouts.tenant')

@section('title', '部門管理')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">部門管理</h1>
    <a href="{{ route('tenant.departments.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        + 新增部門
    </a>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="{{ route('tenant.departments.index') }}" class="flex gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="搜尋部門代碼、名稱..." class="flex-1 border rounded px-3 py-2">
        
        <select name="parent_id" class="border rounded px-3 py-2">
            <option value="">全部部門</option>
            <option value="0" {{ request('parent_id') === '0' ? 'selected' : '' }}>頂層部門</option>
            @foreach(\App\Models\Department::whereNull('parent_id')->orderBy('sort_order')->get() as $dept)
                <option value="{{ $dept->id }}" {{ request('parent_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
        
        <select name="is_active" class="border rounded px-3 py-2">
            <option value="">全部狀態</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>啟用</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>停用</option>
        </select>
        
        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            搜尋
        </button>
        
        @if(request()->hasAny(['search', 'parent_id', 'is_active']))
            <a href="{{ route('tenant.departments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                清除
            </a>
        @endif
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">部門代碼</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">部門名稱</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">上層部門</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">主管</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($departments as $department)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $department->code }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    @if($department->parent_id)
                        <span class="text-gray-400">└ </span>
                    @endif
                    {{ $department->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $department->parent?->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $department->manager?->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($department->is_active)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            啟用
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            停用
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('tenant.departments.show', $department) }}" class="text-blue-600 hover:text-blue-900 mr-3">查看</a>
                    <a href="{{ route('tenant.departments.edit', $department) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">編輯</a>
                    <form action="{{ route('tenant.departments.destroy', $department) }}" method="POST" class="inline" onsubmit="return confirm('確定要刪除此部門嗎？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">刪除</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    沒有找到任何部門資料
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 分頁 -->
<div class="mt-4">
    {{ $departments->links() }}
</div>
@endsection
