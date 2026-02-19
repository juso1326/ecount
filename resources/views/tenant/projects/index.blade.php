@extends('layouts.tenant')

@section('title', '專案管理')

@section('page-title', '專案管理')

@section('content')
<div class="mb-2 flex justify-between items-center">
    <!-- 左側：分頁資訊 -->
    <div class="text-sm text-gray-600 dark:text-gray-400">
        @if($projects->total() > 0)
            顯示第 <span class="font-medium">{{ $projects->firstItem() }}</span> 
            到 <span class="font-medium">{{ $projects->lastItem() }}</span> 筆，
            共 <span class="font-medium">{{ number_format($projects->total()) }}</span> 筆
        @else
            <span>無資料</span>
        @endif
    </div>
    
    <!-- 右側：操作按鈕 -->
    <div class="flex gap-2">
        @if($projects->total() > 0)
        <a href="{{ route('tenant.projects.export', request()->all()) }}" 
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            匯出
        </a>
        @endif
        <a href="{{ route('tenant.projects.create') }}" 
           class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
            + 新增專案
        </a>
    </div>
</div>

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" action="{{ route('tenant.projects.index') }}" class="space-y-4">
        <!-- 智能搜尋框 -->
        <div class="flex gap-2">
            <div class="flex-1">
                <input type="text" name="smart_search" value="{{ request('smart_search') }}" 
                       placeholder="🔍 聰明尋找：專案名稱/代碼/成員/負責人/發票號/報價單號..." 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    💡 提示：輸入任何關鍵字即可搜尋專案、成員、負責人、發票號或報價單號
                </p>
            </div>
            <button type="submit" 
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                搜尋
            </button>
            @if(request()->hasAny(['smart_search', 'date_start', 'date_end', 'status', 'company_id']))
                <a href="{{ route('tenant.projects.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                    清除
                </a>
            @endif
        </div>

        <!-- 進階篩選 -->
        <details class="group">
            <summary class="cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary">
                <span class="inline-block group-open:rotate-90 transition-transform">▶</span>
                進階篩選
            </summary>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <!-- 開案日期範圍 -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">開案日（起）</label>
                    <input type="date" name="date_start" value="{{ request('date_start', $dateStart) }}" 
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">開案日（迄）</label>
                    <input type="date" name="date_end" value="{{ request('date_end', $dateEnd) }}" 
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                </div>
                
                <!-- 狀態篩選 -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">專案狀態</label>
                    <select name="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">全部狀態</option>
                        <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>規劃中</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>進行中</option>
                        <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>暫停</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>已完成</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>已取消</option>
                    </select>
                </div>
                
                <!-- 公司篩選 -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">客戶公司</label>
                    <select name="company_id" id="company_filter" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">全部客戶</option>
                        @foreach(\App\Models\Company::where('is_active', true)->orderBy('name')->get() as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </details>
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">序號</th>
                <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
                <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">開案日</th>
                <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">客戶</th>
                <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">專案名</th>
                <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">標籤</th>
                <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">專案負責</th>
                <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">成員</th>
                <th class="px-3 py-1 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">總額</th>
                <th class="px-3 py-1 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">專案支出</th>
                <th class="px-3 py-1 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">累計</th>
                <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($projects as $index => $project)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                <!-- 序號 -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                    {{ ($projects->currentPage() - 1) * $projects->perPage() + $index + 1 }}
                </td>
                <!-- 操作 -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-center space-x-2">
                    <a href="{{ route('tenant.projects.show', $project) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                        詳細
                    </a>
                    <a href="{{ route('tenant.projects.edit', $project) }}" 
                       class="text-primary hover:text-primary-dark font-medium">
                        編輯
                    </a>
                </td>
                <!-- 開案日 -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $project->start_date?->format('Y-m-d') }}
                </td>
                <!-- 客戶 -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $project->company?->name ?? '-' }}
                </td>
                <!-- 專案名 -->
                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">
                    <div class="max-w-xs truncate" title="{{ $project->name }}">
                        {{ $project->name }}
                    </div>
                    <div class="text-xs text-gray-500">{{ $project->code }}</div>
                </td>
                <!-- 標籤 -->
                <td class="px-3 py-2 text-sm">
                    @if($project->tags && $project->tags->count() > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach($project->tags->take(2) as $tag)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" 
                                  style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                                {{ $tag->name }}
                            </span>
                            @endforeach
                            @if($project->tags->count() > 2)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                +{{ $project->tags->count() - 2 }}
                            </span>
                            @endif
                        </div>
                    @else
                        <span class="text-xs text-gray-400">-</span>
                    @endif
                </td>
                <!-- 專案負責 -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $project->manager?->name ?? '-' }}
                </td>
                <!-- 成員 -->
                <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center space-x-1">
                        @if($project->members && $project->members->count() > 0)
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
                <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white font-medium">
                    ${{ number_format($project->total_receivable ?? 0, 0) }}
                </td>
                <!-- 專案支出 (應付總額) -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                    ${{ number_format($project->total_payable ?? 0, 0) }}
                </td>
                <!-- 累計 (已收 - 已付) -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-right font-medium 
                    {{ ($project->accumulated_income ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    ${{ number_format($project->accumulated_income ?? 0, 0) }}
                </td>
                <!-- 狀態 -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-center">
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
                <td colspan="13" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    沒有找到任何專案資料
                </td>
            </tr>
            @endforelse
        </tbody>
        <!-- 總計行 -->
        <tfoot class="bg-gray-100 dark:bg-gray-700 font-semibold">
            <tr>
                <td colspan="9" class="px-3 py-1 text-right text-sm text-gray-900 dark:text-white">
                    總計
                </td>
                <td class="px-3 py-1 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                    ${{ number_format($totals['total_receivable'] ?? 0, 0) }}
                </td>
                <td class="px-3 py-1 whitespace-nowrap text-sm text-right text-orange-600 dark:text-orange-400">
                    ${{ number_format($totals['withholding_tax'] ?? 0, 0) }}
                </td>
                <td class="px-3 py-1 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                    ${{ number_format($totals['total_payable'] ?? 0, 0) }}
                </td>
                <td class="px-3 py-1 whitespace-nowrap text-sm text-right 
                    {{ ($totals['accumulated_income'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    ${{ number_format($totals['accumulated_income'] ?? 0, 0) }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- 分頁導航 -->
@if($projects->hasPages())
<div class="mt-6">
    {{ $projects->appends(request()->except('page'))->links() }}
</div>
@endif
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#company_filter').select2({
        placeholder: '請選擇客戶公司',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endsection
