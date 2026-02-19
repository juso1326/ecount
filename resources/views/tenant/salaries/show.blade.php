@extends('layouts.tenant')

@section('title', '薪資明細')

@section('page-title', '薪資明細')

@section('content')
<!-- 頁面標題與導航 -->
<div class="mb-3">
    <!-- 月份導航 -->
    <div class="flex items-center justify-between mb-2">
        <a href="{{ route('tenant.salaries.show', ['user' => $user->id, 'year' => $year, 'month' => $month, 'nav' => 'prev']) }}" 
           class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            上個月
        </a>
        
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $salary['period']['label'] }}</h2>
        
        <a href="{{ route('tenant.salaries.show', ['user' => $user->id, 'year' => $year, 'month' => $month, 'nav' => 'next']) }}" 
           class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition">
            下個月
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    
    <!-- 標題與操作按鈕 -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }} 薪資明細</h1>
        <div class="flex gap-2">
            @if(!$isPaid && $salary['total'] > 0)
            <button onclick="document.getElementById('payModal').classList.remove('hidden')"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                確認撥款
            </button>
            @endif
        </div>
    </div>
</div>

<!-- 薪資摘要 -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">基本薪資</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">
            ${{ number_format($salary['base_salary'], 0) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">加項</div>
        <div class="text-2xl font-bold text-green-600">
            +${{ number_format($salary['additions'], 0) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">扣項</div>
        <div class="text-2xl font-bold text-red-600">
            -${{ number_format($salary['deductions'], 0) }}
        </div>
    </div>
    <div class="bg-blue-50 dark:bg-blue-900 shadow-sm rounded-lg border border-blue-200 dark:border-blue-700 p-4">
        <div class="text-sm text-blue-600 dark:text-blue-300">總計</div>
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-300">
            ${{ number_format($salary['total'], 0) }}
        </div>
    </div>
</div>

<!-- 薪資明細 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-3">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">薪資項目</h2>
    </div>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">日期</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">專案</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">內容</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">金額</th>
                @if(!$isPaid)
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">移動</th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($salary['items'] as $item)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $item->payment_date->format('Y/m/d') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                    {{ $item->project->name ?? '-' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                    {{ $item->content ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                    ${{ number_format($item->amount, 0) }}
                </td>
                @if(!$isPaid && !$item->is_salary_paid)
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                    <div class="flex justify-center gap-2">
                        <button onclick="moveItem({{ $item->id }}, 'prev')" 
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                title="移到上個月">
                            &lt;
                        </button>
                        <button onclick="moveItem({{ $item->id }}, 'next')" 
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                title="移到下個月">
                            &gt;
                        </button>
                    </div>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ !$isPaid ? 5 : 4 }}" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">無薪資項目</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 加扣項明細 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-3">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">加扣項明細</h2>
        @if(!$isPaid)
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            新增加扣項
        </button>
        @endif
    </div>
    
    <div class="p-6">
        <!-- 週期性加扣項 -->
        @if($periodicAdjustments->count() > 0)
        <div class="mb-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                週期性加扣項（長期有效）
            </h3>
            <div class="space-y-2">
                @foreach($periodicAdjustments as $adj)
                @php
                    $isExcluded = $adj->isExcludedForMonth($currentYear, $currentMonth);
                    
                    // 計算生效月份範圍
                    $startDate = $adj->start_date ? \Carbon\Carbon::parse($adj->start_date) : null;
                    $endDate = $adj->end_date ? \Carbon\Carbon::parse($adj->end_date) : null;
                    $today = \Carbon\Carbon::now();
                    
                    $effectiveMonths = [];
                    if ($startDate) {
                        $start = $startDate->copy()->startOfMonth();
                        $end = $endDate ? $endDate->copy()->endOfMonth() : $today->copy()->addYears(2)->endOfMonth();
                        
                        $current = $start->copy();
                        while ($current->lte($end)) {
                            $effectiveMonths[] = [
                                'year' => $current->year,
                                'month' => $current->month,
                                'label' => $current->format('Y/m'),
                                'excluded' => $adj->isExcludedForMonth($current->year, $current->month)
                            ];
                            $current->addMonth();
                            if (count($effectiveMonths) >= 24) break; // 最多顯示24個月
                        }
                    }
                @endphp
                <div class="py-3 px-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center justify-between {{ $isExcluded ? 'opacity-50' : '' }}">
                        <div class="flex items-center gap-3 flex-1">
                            <span class="text-gray-400 text-xl">●</span>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $adj->title }}
                                    @if($isExcluded)
                                    <span class="ml-2 text-xs bg-gray-500 text-white px-2 py-0.5 rounded">本月已停用</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $adj->recurrence === 'monthly' ? '每月固定' : '每年固定' }}
                                    @if($adj->start_date)
                                        • 生效期間: {{ \Carbon\Carbon::parse($adj->start_date)->format('Y/m') }} 
                                        ~ {{ $adj->end_date ? \Carbon\Carbon::parse($adj->end_date)->format('Y/m') : '永久' }}
                                    @endif
                                    @if($adj->remark)
                                    <span class="ml-2">• {{ $adj->remark }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-lg font-semibold {{ $adj->type === 'add' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $adj->type === 'add' ? '+' : '-' }}${{ number_format($adj->amount, 0) }}
                            </div>
                            @if(count($effectiveMonths) > 0)
                            <button onclick="toggleExclusionManager({{ $adj->id }})" 
                                    class="text-xs bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded" 
                                    title="管理排除月份">
                                排除月份
                            </button>
                            @endif
                            @if($isExcluded)
                            <form method="POST" action="{{ route('tenant.salaries.restore-adjustment', ['user' => $user->id, 'adjustment' => $adj->id]) }}" class="inline">
                                @csrf
                                <input type="hidden" name="year" value="{{ $currentYear }}">
                                <input type="hidden" name="month" value="{{ $currentMonth }}">
                                <button type="submit" class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded" title="恢復本月">
                                    恢復
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('tenant.salaries.exclude-adjustment', ['user' => $user->id, 'adjustment' => $adj->id]) }}" class="inline">
                                @csrf
                                <input type="hidden" name="year" value="{{ $currentYear }}">
                                <input type="hidden" name="month" value="{{ $currentMonth }}">
                                <button type="submit" class="text-xs bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-1 rounded" title="本月停用">
                                    停用
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    
                    @if(count($effectiveMonths) > 0)
                    <!-- 月份排除管理區 -->
                    <div id="exclusion-manager-{{ $adj->id }}" class="hidden mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                            💡 點擊月份可切換該月是否發放此加扣項（已排除月份會顯示為灰色）
                        </div>
                        <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-12 gap-2">
                            @foreach($effectiveMonths as $month)
                            <form method="POST" 
                                  action="{{ $month['excluded'] 
                                      ? route('tenant.salaries.restore-adjustment', ['user' => $user->id, 'adjustment' => $adj->id])
                                      : route('tenant.salaries.exclude-adjustment', ['user' => $user->id, 'adjustment' => $adj->id]) }}" 
                                  class="inline">
                                @csrf
                                <input type="hidden" name="year" value="{{ $month['year'] }}">
                                <input type="hidden" name="month" value="{{ $month['month'] }}">
                                <button type="submit" 
                                        class="w-full text-xs px-2 py-1 rounded {{ $month['excluded'] 
                                            ? 'bg-gray-300 text-gray-500 line-through' 
                                            : 'bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-500 border border-gray-300 dark:border-gray-500' }}"
                                        title="{{ $month['excluded'] ? '點擊恢復' : '點擊排除' }}">
                                    {{ $month['label'] }}
                                </button>
                            </form>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- 單次加扣項 -->
        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                單次加扣項（本月有效）
            </h3>
            <div class="space-y-2">
                @forelse($onceAdjustments as $adj)
                <div class="flex items-center justify-between py-3 px-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3">
                        <span class="text-blue-400 text-xl">○</span>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $adj->title }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $adj->start_date->format('Y-m-d') }}
                                @if($adj->end_date)
                                ~ {{ $adj->end_date->format('Y-m-d') }}
                                @endif
                                @if($adj->remark)
                                <span class="ml-2">• {{ $adj->remark }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-lg font-semibold {{ $adj->type === 'add' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $adj->type === 'add' ? '+' : '-' }}${{ number_format($adj->amount, 0) }}
                        </div>
                        @if(!$isPaid)
                        <div class="flex gap-2">
                            <button onclick="editAdjustment({{ $adj->id }}, '{{ $adj->title }}', {{ $adj->amount }}, '{{ $adj->type }}', '{{ addslashes($adj->remark ?? '') }}')" 
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                編輯
                            </button>
                            <button onclick="deleteAdjustment({{ $adj->id }}, '{{ $adj->title }}')" 
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                刪除
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm">本月無單次加扣項</p>
                    @if(!$isPaid)
                    <button onclick="openAddModal()" class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                        點擊新增
                    </button>
                    @endif
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- 撥款彈窗 -->
<div id="payModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-2">確認薪資撥款</h3>
            <form action="{{ route('tenant.salaries.pay', $user) }}" method="POST">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">
                
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">應付總額</label>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        ${{ number_format($salary['total'], 0) }}
                    </div>
                </div>
                
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">實發金額 *</label>
                    <input type="number" name="actual_amount" 
                           value="{{ $salary['total'] }}"
                           step="0.01" min="0"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"
                           required>
                </div>
                
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">備註</label>
                    <textarea name="remark" rows="3"
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"></textarea>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        確認撥款
                    </button>
                    <button type="button" onclick="document.getElementById('payModal').classList.add('hidden')"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        取消
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

<!-- 新增加扣項彈窗 -->
<div id="addAdjustmentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">新增加扣項</h3>
                <button onclick="closeModal('addAdjustmentModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="addAdjustmentForm" onsubmit="addAdjustment(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">類型 *</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="adj_type" value="add" checked class="mr-2">
                            <span class="text-green-600 font-medium">加項</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="adj_type" value="deduct" class="mr-2">
                            <span class="text-red-600 font-medium">扣項</span>
                        </label>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">項目名稱 *</label>
                    <input type="text" id="adj_title" maxlength="100"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"
                           placeholder="例如：績效獎金、請假扣薪" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">金額 *</label>
                    <input type="number" id="adj_amount" step="0.01" min="0"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"
                           placeholder="0.00" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">週期類型 *</label>
                    <select id="adj_recurrence" onchange="toggleRecurrenceFields()" 
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="once">單次（指定期間）</option>
                        <option value="monthly">每月固定</option>
                        <option value="yearly">每年固定</option>
                    </select>
                </div>
                
                <div id="date_range_fields" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">生效期間 *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <input type="date" id="adj_start_date" 
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">開始日</p>
                        </div>
                        <div>
                            <input type="date" id="adj_end_date" 
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                            <p class="text-xs text-gray-500 mt-1">結束日（選填）</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        💡 單次：指定起訖日期 | 固定：開始日為生效起始月份，結束日為終止月份（不填則永久有效）
                    </p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">備註</label>
                    <textarea id="adj_remark" rows="2" maxlength="500"
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"
                              placeholder="選填"></textarea>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        確認新增
                    </button>
                    <button type="button" onclick="closeModal('addAdjustmentModal')"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                        取消
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 編輯加扣項彈窗 -->
<div id="editAdjustmentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">編輯加扣項</h3>
                <button onclick="closeModal('editAdjustmentModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="editAdjustmentForm" onsubmit="updateAdjustment(event)">
                <input type="hidden" id="edit_adj_id">
                <input type="hidden" id="edit_adj_type">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">項目名稱 *</label>
                    <input type="text" id="edit_adj_title" maxlength="100"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"
                           required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">金額 *</label>
                    <input type="number" id="edit_adj_amount" step="0.01" min="0"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"
                           required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">備註</label>
                    <textarea id="edit_adj_remark" rows="2" maxlength="500"
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"></textarea>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        確認更新
                    </button>
                    <button type="button" onclick="closeModal('editAdjustmentModal')"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                        取消
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// 開關彈窗
function openAddModal() {
    document.getElementById('addAdjustmentModal').classList.remove('hidden');
    document.getElementById('addAdjustmentForm').reset();
    // 設定預設開始日期為本月1號
    const today = new Date();
    const year = {{ $year }};
    const month = ('{{ $month }}').padStart(2, '0');
    document.getElementById('adj_start_date').value = `${year}-${month}-01`;
    toggleRecurrenceFields();
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function toggleRecurrenceFields() {
    const recurrence = document.getElementById('adj_recurrence').value;
    const dateFields = document.getElementById('date_range_fields');
    const startDate = document.getElementById('adj_start_date');
    const endDate = document.getElementById('adj_end_date');
    
    if (recurrence === 'once') {
        // 單次：需要起訖日期
        startDate.required = true;
        endDate.required = false;
        dateFields.querySelector('.text-xs.text-gray-500.mt-1:last-child').textContent = 
            '💡 單次：指定起訖日期 | 固定：開始日為生效起始月份，結束日為終止月份（不填則永久有效）';
    } else {
        // 固定：開始日為生效起始月份
        startDate.required = true;
        endDate.required = false;
    }
}

// 切換排除月份管理區
function toggleExclusionManager(adjId) {
    const manager = document.getElementById(`exclusion-manager-${adjId}`);
    if (manager.classList.contains('hidden')) {
        manager.classList.remove('hidden');
    } else {
        manager.classList.add('hidden');
    }
}

// 新增加扣項
async function addAdjustment(event) {
    event.preventDefault();
    
    const formData = {
        type: document.querySelector('input[name="adj_type"]:checked').value,
        title: document.getElementById('adj_title').value,
        amount: parseFloat(document.getElementById('adj_amount').value),
        recurrence: document.getElementById('adj_recurrence').value,
        start_date: document.getElementById('adj_start_date').value,
        end_date: document.getElementById('adj_end_date').value || null,
        year: {{ $year }},
        month: {{ $month }},
        remark: document.getElementById('adj_remark').value
    };
    
    try {
        const response = await fetch('{{ route("tenant.salaries.quick-adjustment.store", $user) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('success', result.message);
            closeModal('addAdjustmentModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', result.message);
        }
    } catch (error) {
        showToast('error', '操作失敗，請稍後再試');
        console.error(error);
    }
}

// 編輯加扣項
function editAdjustment(id, title, amount, type, remark) {
    document.getElementById('edit_adj_id').value = id;
    document.getElementById('edit_adj_type').value = type;
    document.getElementById('edit_adj_title').value = title;
    document.getElementById('edit_adj_amount').value = amount;
    document.getElementById('edit_adj_remark').value = remark || '';
    document.getElementById('editAdjustmentModal').classList.remove('hidden');
}

async function updateAdjustment(event) {
    event.preventDefault();
    
    const adjustmentId = document.getElementById('edit_adj_id').value;
    const formData = {
        title: document.getElementById('edit_adj_title').value,
        amount: parseFloat(document.getElementById('edit_adj_amount').value),
        remark: document.getElementById('edit_adj_remark').value
    };
    
    try {
        const response = await fetch(`{{ url('/salaries/adjustments') }}/${adjustmentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('success', result.message);
            closeModal('editAdjustmentModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', result.message);
        }
    } catch (error) {
        showToast('error', '更新失敗');
        console.error(error);
    }
}

// 刪除加扣項
async function deleteAdjustment(adjustmentId, title) {
    if (!confirm(`確定要刪除「${title}」嗎？\n刪除後將立即從薪資中移除。`)) {
        return;
    }
    
    try {
        const response = await fetch(`{{ url('/salaries/adjustments') }}/${adjustmentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('success', '刪除成功');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', result.message);
        }
    } catch (error) {
        showToast('error', '刪除失敗');
        console.error(error);
    }
}

// Toast 提示
function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// 移動薪資項目（保留原功能）
function moveItem(payableId, direction) {
    if (!confirm('確定要移動此薪資項目到' + (direction === 'prev' ? '上個月' : '下個月') + '嗎？')) {
        return;
    }
    
    const url = direction === 'prev' 
        ? '{{ route("tenant.salaries.move-prev") }}'
        : '{{ route("tenant.salaries.move-next") }}';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ payable_id: payableId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('錯誤：' + data.message);
        }
    })
    .catch(error => {
        alert('操作失敗，請稍後再試');
        console.error('Error:', error);
    });
}
</script>
@endsection
