@extends('layouts.tenant')

@section('title', '專案管理')

@section('page-title', '專案管理')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">專案帳戶管理 &gt; 專案管理</p>
</div>

<!-- 頁面標題與按鈕 -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">專案管理</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            第 {{ $projects->currentPage() }} / {{ $projects->lastPage() }} 頁，每頁15筆，共{{ $projects->total() }}筆
        </p>
    </div>
    <a href="{{ route('tenant.projects.create') }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增專案
    </a>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <form method="GET" action="{{ route('tenant.projects.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- 搜尋框 -->
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="搜尋專案代碼、名稱..." 
               class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
        
        <!-- 狀態篩選 -->
        <select name="status" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">全部狀態</option>
            <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>規劃中</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>進行中</option>
            <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>暫停</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>已完成</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>已取消</option>
        </select>
        
        <!-- 公司篩選 -->
        <select name="company_id" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">全部客戶</option>
            @foreach(\App\Models\Company::where('is_active', true)->orderBy('name')->get() as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
            @endforeach
        </select>
        
        <!-- 部門篩選 -->
        <select name="department_id" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">全部部門</option>
            @foreach(\App\Models\Department::where('is_active', true)->orderBy('name')->get() as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
        
        <!-- 按鈕 -->
        <div class="flex gap-2">
            <button type="submit" 
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg flex-1">
                搜尋
            </button>
            
            @if(request()->hasAny(['search', 'status', 'company_id', 'department_id']))
                <a href="{{ route('tenant.projects.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
                    清除
                </a>
            @endif
        </div>
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">序號</th>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">編輯</th>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">詳細</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">開案日</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">客戶</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">專案名</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">類型</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">專案負責</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">成員</th>
                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">總額</th>
                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">扣繳</th>
                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">專案支出</th>
                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">累計</th>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($projects as $index => $project)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                <!-- 序號 -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                    {{ ($projects->currentPage() - 1) * $projects->perPage() + $index + 1 }}
                </td>
                <!-- 編輯 -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                    <a href="{{ route('tenant.projects.edit', $project) }}" 
                       class="text-primary hover:text-primary-dark font-medium">
                        編輯
                    </a>
                </td>
                <!-- 詳細 -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                    <a href="{{ route('tenant.projects.show', $project) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                        詳細
                    </a>
                </td>
                <!-- 開案日 -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $project->start_date?->format('Y-m-d') }}
                </td>
                <!-- 客戶 -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $project->company?->name ?? '-' }}
                </td>
                <!-- 專案名 -->
                <td class="px-3 py-4 text-sm text-gray-900 dark:text-white">
                    <div class="max-w-xs truncate" title="{{ $project->name }}">
                        {{ $project->name }}
                    </div>
                    <div class="text-xs text-gray-500">{{ $project->code }}</div>
                </td>
                <!-- 類型 -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $project->project_type ?? '-' }}
                </td>
                <!-- 專案負責 -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $project->manager?->name ?? '-' }}
                </td>
                <!-- 成員 -->
                <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center space-x-1">
                        @if($project->members?->count() > 0)
                            <div class="flex -space-x-2">
                                @foreach($project->members->take(3) as $member)
                                <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center text-xs font-medium border-2 border-white dark:border-gray-800" 
                                     title="{{ $member->name }}">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                @endforeach
                            </div>
                            @if($project->members->count() > 3)
                            <span class="text-xs text-gray-500">+{{ $project->members->count() - 3 }}</span>
                            @endif
                        @else
                            <span class="text-xs text-gray-400">無成員</span>
                        @endif
                    </div>
                </td>
                <!-- 總額 (應收總額) -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white font-medium">
                    ${{ number_format($project->total_receivable ?? 0, 0) }}
                </td>
                <!-- 扣繳 -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-right text-orange-600 dark:text-orange-400">
                    ${{ number_format($project->withholding_tax ?? 0, 0) }}
                </td>
                <!-- 專案支出 (應付總額) -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                    ${{ number_format($project->total_payable ?? 0, 0) }}
                </td>
                <!-- 累計 (已收 - 已付) -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-medium 
                    {{ ($project->accumulated_income ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    ${{ number_format($project->accumulated_income ?? 0, 0) }}
                </td>
                <!-- 狀態 -->
                <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                    @if($project->status === 'in_progress')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            進行中
                        </span>
                    @elseif($project->status === 'completed')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            已完成
                        </span>
                    @elseif($project->status === 'planning')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            規劃中
                        </span>
                    @elseif($project->status === 'on_hold')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                            暫停
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            已取消
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="14" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    沒有找到任何專案資料
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 分頁 -->
<div class="mt-6">
    {{ $projects->appends(request()->except('page'))->links() }}
</div>
@endsection
