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
            @if(request()->hasAny(['smart_search', 'date_start', 'date_end', 'status', 'company_id', 'date_mode', 'show_closed']))
                <a href="{{ route('tenant.projects.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                    清除
                </a>
            @endif
        </div>

        <!-- 日期模式 + 已結案勾選 -->
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-600 dark:text-gray-400 font-medium">日期範圍：</span>
                <label class="flex items-center gap-1 cursor-pointer">
                    <input type="radio" name="date_mode" value="last_year" {{ ($dateMode ?? 'last_year') === 'last_year' ? 'checked' : '' }} onchange="toggleCustomDate(this.value)" class="text-primary">
                    <span class="text-gray-700 dark:text-gray-300">最近一年</span>
                </label>
                <label class="flex items-center gap-1 cursor-pointer">
                    <input type="radio" name="date_mode" value="this_year" {{ ($dateMode ?? '') === 'this_year' ? 'checked' : '' }} onchange="toggleCustomDate(this.value)" class="text-primary">
                    <span class="text-gray-700 dark:text-gray-300">本年度</span>
                </label>
                <label class="flex items-center gap-1 cursor-pointer">
                    <input type="radio" name="date_mode" value="custom" {{ ($dateMode ?? '') === 'custom' ? 'checked' : '' }} onchange="toggleCustomDate(this.value)" class="text-primary">
                    <span class="text-gray-700 dark:text-gray-300">自定義</span>
                </label>
            </div>
            <div id="custom_date_range" class="{{ ($dateMode ?? 'last_year') === 'custom' ? 'flex' : 'hidden' }} items-center gap-2">
                <input type="date" name="date_start" value="{{ $dateMode === 'custom' ? $dateStart : '' }}"
                       class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-sm">
                <span class="text-gray-500">～</span>
                <input type="date" name="date_end" value="{{ $dateMode === 'custom' ? $dateEnd : '' }}"
                       class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-sm">
            </div>
            <label class="flex items-center gap-2 cursor-pointer text-sm">
                <input type="checkbox" name="show_closed" value="1" {{ ($showClosed ?? false) ? 'checked' : '' }} class="rounded text-primary">
                <span class="text-gray-700 dark:text-gray-300">列出已結案</span>
            </label>
        </div>

        <!-- 進階篩選 -->
        <details class="group" {{ request()->hasAny(['status', 'company_id']) ? 'open' : '' }}>
            <summary class="cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary">
                <span class="inline-block group-open:rotate-90 transition-transform">▶</span>
                進階篩選
            </summary>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <!-- 狀態篩選 -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">專案狀態</label>
                    <select name="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">全部狀態</option>
                        @foreach($projectStatuses as $ps)
                            <option value="{{ $ps['value'] }}" {{ request('status') === $ps['value'] ? 'selected' : '' }}>{{ $ps['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- 公司篩選 -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">客戶公司</label>
                    <select name="company_id" id="company_filter" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">全部客戶</option>
                        @foreach(\App\Models\Company::where('is_active', true)->orderBy('short_name')->get() as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->short_name ?? $company->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </details>
    </form>
</div>

<script>
function toggleCustomDate(mode) {
    document.getElementById('custom_date_range').classList.toggle('hidden', mode !== 'custom');
    document.getElementById('custom_date_range').classList.toggle('flex', mode === 'custom');
}
</script>

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">操作</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">開案日</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">公司簡稱</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">專案名</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">類型</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">專案負責</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">成員</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">總額</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">扣繳</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">專案支出</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">累計</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">狀態</th>
            </tr>
            <!-- 本頁總計 -->
            <tr class="bg-blue-50 dark:bg-blue-900/30 border-t border-gray-200 dark:border-gray-500 font-semibold">
                <th colspan="7" class="px-3 py-2 text-right text-xs text-gray-600 dark:text-gray-200">搜尋結果總計</th>
                <th class="px-3 py-2 text-right text-xs text-gray-900 dark:text-white whitespace-nowrap">NT$ {{ fmt_num($totals['total_receivable'] ?? 0) }}</th>
                <th class="px-3 py-2 text-right text-xs text-orange-600 dark:text-orange-400 whitespace-nowrap">NT$ {{ fmt_num($totals['withholding_tax'] ?? 0) }}</th>
                <th class="px-3 py-2 text-right text-xs text-red-600 dark:text-red-400 whitespace-nowrap">NT$ {{ fmt_num($totals['total_payable'] ?? 0) }}</th>
                <th class="px-3 py-2 text-right text-xs whitespace-nowrap {{ ($totals['accumulated_income'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">NT$ {{ fmt_num($totals['accumulated_income'] ?? 0) }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($projects as $index => $project)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
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
                    {{ format_date($project->start_date) }}
                </td>
                <!-- 客戶 -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $project->company?->short_name ?? $project->company?->name ?? '-' }}
                </td>
                <!-- 專案名 -->
                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">
                    <div class="max-w-xs truncate" title="{{ $project->name }}">
                        {{ $project->name }}
                    </div>
                </td>
                <!-- 類型 -->
                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ $project->project_type ?? '-' }}
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
                            <span class="text-sm text-gray-500">+{{ $project->members->count() - 3 }}</span>
                            @endif
                        @else
                            <span class="text-sm text-gray-400">無成員</span>
                        @endif
                    </div>
                </td>
                <!-- 總額 (應收總額) -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white font-medium">
                    ${{ fmt_num($project->total_receivable ?? 0) }}
                </td>
                <!-- 扣繳 -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-orange-600 dark:text-orange-400">
                    ${{ fmt_num($project->withholding_tax ?? 0) }}
                </td>
                <!-- 專案支出 (應付總額) -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                    ${{ fmt_num($project->total_payable ?? 0) }}
                </td>
                <!-- 累計 (已收 - 已付) -->
                <td class="px-3 py-2 whitespace-nowrap text-sm text-right font-medium 
                    {{ ($project->accumulated_income ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    ${{ fmt_num($project->accumulated_income ?? 0) }}
                </td>
                <!-- 狀態 -->
                <td class="px-3 py-2 whitespace-nowrap text-xs text-center">
                    @php
                        $statusMap = collect($projectStatuses)->keyBy('value');
                        $ps = $statusMap->get($project->status);
                        $hexColor = $ps['color'] ?? '#6b7280';
                    @endphp
                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full"
                          style="color: {{ $hexColor }}; background-color: {{ $hexColor }}1a;">
                        {{ $ps['label'] ?? $project->status }}
                    </span>
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
        <!-- 總計行已移至 thead 第二列 -->
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
