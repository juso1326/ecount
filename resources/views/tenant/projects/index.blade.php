@extends('layouts.tenant')

@section('title', '專案管理')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">專案管理</h1>
    <a href="{{ route('tenant.projects.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        + 新增專案
    </a>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="{{ route('tenant.projects.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="搜尋專案代碼、名稱..." class="border rounded px-3 py-2">
        
        <select name="status" class="border rounded px-3 py-2">
            <option value="">全部狀態</option>
            <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>規劃中</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>進行中</option>
            <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>暫停</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>已完成</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>已取消</option>
        </select>
        
        <select name="company_id" class="border rounded px-3 py-2">
            <option value="">全部公司</option>
            @foreach(\App\Models\Company::where('is_active', true)->orderBy('name')->get() as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
            @endforeach
        </select>
        
        <select name="department_id" class="border rounded px-3 py-2">
            <option value="">全部部門</option>
            @foreach(\App\Models\Department::where('is_active', true)->orderBy('name')->get() as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex-1">
                搜尋
            </button>
            
            @if(request()->hasAny(['search', 'status', 'company_id', 'department_id']))
                <a href="{{ route('tenant.projects.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    清除
                </a>
            @endif
        </div>
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">專案代碼</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">專案名稱</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">公司</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">預算</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">開始日期</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($projects as $project)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $project->code }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $project->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->company->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $statusColors = [
                            'planning' => 'bg-gray-100 text-gray-800',
                            'in_progress' => 'bg-blue-100 text-blue-800',
                            'on_hold' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusNames = [
                            'planning' => '規劃中',
                            'in_progress' => '進行中',
                            'on_hold' => '暫停',
                            'completed' => '已完成',
                            'cancelled' => '已取消',
                        ];
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusNames[$project->status] ?? $project->status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($project->budget) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $project->start_date ? $project->start_date->format('Y-m-d') : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('tenant.projects.show', $project) }}" class="text-blue-600 hover:text-blue-900 mr-3">查看</a>
                    <a href="{{ route('tenant.projects.edit', $project) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">編輯</a>
                    <form action="{{ route('tenant.projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('確定要刪除此專案嗎？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">刪除</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    沒有找到任何專案資料
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 分頁 -->
<div class="mt-4">
    {{ $projects->links() }}
</div>
@endsection
