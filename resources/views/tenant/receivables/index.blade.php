@extends('layouts.tenant')

@section('title', '應收帳款管理')

@section('page-title', '應收帳款管理')

@section('content')
<div class="mb-2 flex justify-between items-center">
    <!-- 左側：分頁資訊 -->
    <div class="text-sm text-gray-600 dark:text-gray-400">
        @if($receivables->total() > 0)
            顯示第 <span class="font-medium">{{ $receivables->firstItem() }}</span> 
            到 <span class="font-medium">{{ $receivables->lastItem() }}</span> 筆，
            共 <span class="font-medium">{{ number_format($receivables->total()) }}</span> 筆
        @else
            <span>無資料</span>
        @endif
    </div>
    
    <!-- 右側：操作按鈕 -->
    <div class="flex gap-2">
        @if($receivables->total() > 0)
        <a href="{{ route('tenant.receivables.export', request()->all()) }}" 
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            匯出
        </a>
        @endif
        <a href="{{ route('tenant.receivables.create') }}" 
           class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
            + 新增應收帳款
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-1 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-2">
    <form method="GET" action="{{ route('tenant.receivables.index') }}" class="space-y-4">
        <!-- 智能搜尋框 -->
        <div class="flex gap-2">
            <div class="flex-1">
                <input type="text" name="smart_search" value="{{ request('smart_search') }}" 
                       placeholder="🔍 聰明尋找：單號/專案/客戶/負責人/發票號/報價單號..." 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    💡 提示：輸入任何關鍵字即可搜尋單號、專案、客戶、負責人、發票號或報價單號
                </p>
            </div>
            <button type="submit" 
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg whitespace-nowrap">
                搜尋
            </button>
            @if(request()->hasAny(['smart_search', 'project_id', 'fiscal_year']))
                <a href="{{ route('tenant.receivables.index') }}" 
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
                <!-- 帳務年度 -->
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">帳務年度</label>
                    <select name="fiscal_year" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">全部年度</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ request('fiscal_year', date('Y')) == $year ? 'selected' : '' }}>
                                {{ $year }} 年
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 專案篩選 -->
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">專案</label>
                    <select name="project_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">全部專案</option>
                        @foreach(\App\Models\Project::where('is_active', true)->orderBy('code')->get() as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->code }} - {{ $project->name }}
                            </option>
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
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="width:50px">序號</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="width:50px">編輯</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="width:50px">入帳</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:80px">負責人</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">開立日</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">客戶</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:160px">專案/內容</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">統編</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">報價單號</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:100px">發票號碼</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">未稅額</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:60px">稅</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">應收</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">入帳日</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:90px">實收</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:70px">扣繳</th>
                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" style="min-width:70px">狀態</th>
            </tr>
            @if($receivables->total() > 0)
            <tr class="bg-blue-50 dark:bg-blue-900/30">
                <td colspan="12" class="px-4 py-2 text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                    總計（{{ $receivables->total() }}筆）：
                </td>
                <td class="px-4 py-2 text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                    NT$ {{ number_format($totalAmount, $decimalPlaces) }}
                </td>
                <td></td>
                <td class="px-4 py-2 text-right text-sm font-bold text-green-600 dark:text-green-400">
                    NT$ {{ number_format($totalReceived, $decimalPlaces) }}
                </td>
                <td class="px-4 py-2 text-right text-sm font-bold text-orange-600 dark:text-orange-400">
                    NT$ {{ number_format($totalWithholding, $decimalPlaces) }}
                </td>
                <td></td>
            </tr>
            @endif
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($receivables as $index => $receivable)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <!-- 序號 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">
                        {{ ($receivables->currentPage() - 1) * $receivables->perPage() + $index + 1 }}
                    </td>
                    <!-- 操作 -->
                    <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium">
                        <a href="{{ route('tenant.receivables.edit', $receivable) }}" 
                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">編輯</a>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium">
                        <button onclick="openQuickReceiveModal(
                            {{ $receivable->id }},
                            {{ $receivable->remaining_amount }},
                            '{{ addslashes($receivable->receipt_no ?? '') }}',
                            '{{ addslashes($receivable->project->name ?? '') }}',
                            '{{ addslashes($receivable->company->tax_id ?? '') }}',
                            '{{ addslashes($receivable->company?->short_name ?? $receivable->company?->name ?? '') }}',
                            '{{ addslashes($receivable->content ?? '') }}',
                            {{ $receivable->amount }}
                        )" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">入帳</button>
                    </td>
                    <!-- 負責人 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $receivable->responsibleUser?->name ?? '-' }}
                    </td>
                    <!-- 開立日 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        @date($receivable->receipt_date)
                    </td>
                    <!-- 客戶 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $receivable->company?->short_name ?? $receivable->company?->name ?? '-' }}
                    </td>
                    <!-- 專案/內容 -->
                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 max-w-xs">
                        <div class="truncate" title="{{ ($receivable->project?->name ?? '') . ($receivable->content ? ' ' . $receivable->content : '') }}">
                            @if($receivable->project && $receivable->content)
                                {{ $receivable->project->name }} : {{ Str::limit($receivable->content, 20) }}
                            @elseif($receivable->project)
                                {{ $receivable->project->name }}
                            @else
                                {{ $receivable->content ? Str::limit($receivable->content, 25) : '-' }}
                            @endif
                        </div>
                    </td>
                    <!-- 統編 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $receivable->company?->tax_id ?? '-' }}
                    </td>
                    <!-- 報價單號 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $receivable->quote_no ?? '-' }}
                    </td>
                    <!-- 發票號碼 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $receivable->invoice_no ?? '-' }}
                    </td>
                    <!-- 未稅額 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                        NT$ {{ number_format($receivable->amount_before_tax ?? 0, $decimalPlaces) }}
                    </td>
                    <!-- 稅 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                        NT$ {{ number_format($receivable->tax_amount ?? 0, $decimalPlaces) }}
                    </td>
                    <!-- 應收 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100 font-medium">
                        NT$ {{ number_format($receivable->amount, $decimalPlaces) }}
                    </td>
                    <!-- 入帳日 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @date($receivable->paid_date)
                    </td>
                    <!-- 實收 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400 font-medium">
                        NT$ {{ number_format($receivable->received_amount ?? 0, $decimalPlaces) }}
                    </td>
                    <!-- 扣繳 -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-orange-600 dark:text-orange-400">
                        NT$ {{ number_format($receivable->withholding_tax ?? 0, $decimalPlaces) }}
                    </td>
                    <!-- 狀態 -->
                    <td class="px-3 py-2 whitespace-nowrap text-center">
                        @if($receivable->status === 'paid')
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">已收</span>
                        @elseif($receivable->status === 'partial')
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">部分</span>
                        @elseif($receivable->status === 'overdue')
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">逾期</span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">待收</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="17" class="px-4 py-1 text-center text-gray-500 dark:text-gray-400 text-sm">
                        目前沒有應收帳款資料
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 分頁導航 -->
@if($receivables->hasPages())
<div class="mt-6">
    {{ $receivables->withQueryString()->links() }}
</div>
@endif

<!-- 快速收款 Modal -->
<div id="quickReceiveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[520px] max-w-full shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal 標題 -->
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-1">
                快速收款 - <span id="modalReceiptNo"></span>
            </h3>
            <div class="mb-4 text-sm text-gray-500 dark:text-gray-400 space-y-0.5">
                <div id="modalCompanyName" class="hidden">客戶：<span id="modalCompanyNameText" class="text-gray-700 dark:text-gray-200 font-medium"></span></div>
                <div id="modalContent" class="hidden">說明：<span id="modalContentText" class="text-gray-700 dark:text-gray-200 font-medium"></span></div>
                <div id="modalProjectName" class="hidden">專案：<span id="modalProjectNameText" class="text-gray-700 dark:text-gray-200 font-medium"></span></div>
                <div id="modalTaxId" class="hidden">統編：<span id="modalTaxIdText" class="text-gray-700 dark:text-gray-200 font-medium"></span></div>
                <div id="modalTotalAmount" class="hidden">帳款金額：NT$ <span id="modalTotalAmountText" class="text-gray-700 dark:text-gray-200 font-medium"></span></div>
            </div>
            
            <!-- 表單 -->
            <form id="quickReceiveForm" method="POST">
                @csrf
                <input type="hidden" id="receivableId" name="receivable_id">
                
                <!-- 收款日期 -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        收款日期 <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="payment_date" id="payment_date" required
                           value="{{ date('Y-m-d') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <!-- 收款金額 -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        收款金額 <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount" id="amount" required
                           min="0" step="1"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        未收金額：NT$ <span id="remainingAmount">0</span>
                    </p>
                </div>
                
                <!-- 付款方式 -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        付款方式
                    </label>
                    <select name="payment_method" id="payment_method"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">請選擇</option>
                        @foreach($paymentMethods as $m)
                            <option value="{{ $m->name }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- 備註 -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        備註
                    </label>
                    <textarea name="note" id="note" rows="2"
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                              placeholder="輸入收款備註..."></textarea>
                </div>
                
                <!-- 按鈕 -->
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeQuickReceiveModal()"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg">
                        取消
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg">
                        確認收款
                    </button>
                </div>
            </form>

            <!-- 已收款列表 -->
            <div id="paymentHistorySection" class="mt-5 border-t pt-4 dark:border-gray-600">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">已收款記錄</h4>
                <div id="paymentHistoryList">
                    <p class="text-sm text-gray-400 text-center py-2">載入中...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let _currentReceivableId = null;

function openQuickReceiveModal(receivableId, remainingAmount, receiptNo, projectName, taxId, companyName, content, totalAmount) {
    _currentReceivableId = receivableId;
    document.getElementById('receivableId').value = receivableId;
    document.getElementById('amount').value = remainingAmount;
    document.getElementById('remainingAmount').textContent = new Intl.NumberFormat().format(remainingAmount);
    document.getElementById('modalReceiptNo').textContent = receiptNo;

    const cEl = document.getElementById('modalCompanyName');
    if (companyName) { document.getElementById('modalCompanyNameText').textContent = companyName; cEl.classList.remove('hidden'); }
    else { cEl.classList.add('hidden'); }

    const ctEl = document.getElementById('modalContent');
    if (content) { document.getElementById('modalContentText').textContent = content; ctEl.classList.remove('hidden'); }
    else { ctEl.classList.add('hidden'); }

    const pEl = document.getElementById('modalProjectName');
    if (projectName) { document.getElementById('modalProjectNameText').textContent = projectName; pEl.classList.remove('hidden'); }
    else { pEl.classList.add('hidden'); }

    const tEl = document.getElementById('modalTaxId');
    if (taxId) { document.getElementById('modalTaxIdText').textContent = taxId; tEl.classList.remove('hidden'); }
    else { tEl.classList.add('hidden'); }

    const taEl = document.getElementById('modalTotalAmount');
    if (totalAmount !== undefined) { document.getElementById('modalTotalAmountText').textContent = new Intl.NumberFormat().format(totalAmount); taEl.classList.remove('hidden'); }
    else { taEl.classList.add('hidden'); }

    // 已收款完畢則隱藏新增表單
    const formEl = document.getElementById('quickReceiveForm');
    if (remainingAmount <= 0) { formEl.classList.add('hidden'); }
    else { formEl.classList.remove('hidden'); }

    document.getElementById('quickReceiveModal').classList.remove('hidden');
    document.getElementById('quickReceiveForm').action = `/receivable-payments/${receivableId}`;

    loadPaymentHistory(receivableId);
}

function loadPaymentHistory(receivableId) {
    const list = document.getElementById('paymentHistoryList');
    list.innerHTML = '<p class="text-sm text-gray-400 text-center py-2">載入中...</p>';
    fetch(`/receivable-payments/${receivableId}/list`)
        .then(r => r.json())
        .then(payments => {
            if (!payments.length) {
                list.innerHTML = '<p class="text-sm text-gray-400 text-center py-2">尚無收款記錄</p>';
                return;
            }
            let html = '<table class="w-full text-sm">';
            html += '<thead><tr class="text-xs text-gray-500 dark:text-gray-400 border-b dark:border-gray-600">';
            html += '<th class="text-left pb-1 pr-2">#</th><th class="text-left pb-1">日期</th><th class="text-left pb-1">方式</th><th class="text-right pb-1">金額</th><th class="text-left pb-1 pl-2">備註</th><th class="pb-1"></th>';
            html += '</tr></thead><tbody>';
            payments.forEach((p, i) => {
                html += `<tr class="border-b dark:border-gray-700 last:border-0">
                    <td class="py-1 pr-2 text-gray-400 text-xs">${i + 1}</td>
                    <td class="py-1 pr-2 whitespace-nowrap">${p.payment_date ? p.payment_date.substring(0, 10) : '-'}</td>
                    <td class="py-1 pr-2 text-gray-600 dark:text-gray-300">${p.payment_method ?? '-'}</td>
                    <td class="py-1 pr-2 text-right font-medium">NT$${Number(p.amount).toLocaleString()}</td>
                    <td class="py-1 pl-2 text-gray-500 dark:text-gray-400 text-xs max-w-[80px] truncate">${p.note ?? ''}</td>
                    <td class="py-1 pl-1">
                        <button onclick="deletePayment(${p.id})" class="text-red-400 hover:text-red-600 text-xs">刪除</button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            list.innerHTML = html;
        })
        .catch(() => { list.innerHTML = '<p class="text-sm text-red-400 text-center py-2">載入失敗</p>'; });
}

function deletePayment(paymentId) {
    if (!confirm('確定刪除此筆收款記錄？')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/receivable-payments/${paymentId}`;
    form.innerHTML = `@csrf @method('DELETE')`;
    // Use proper tokens
    const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}';
    const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE';
    form.appendChild(csrf); form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
}

function closeQuickReceiveModal() {
    document.getElementById('quickReceiveModal').classList.add('hidden');
    document.getElementById('quickReceiveForm').reset();
}

// 驗證收款金額不超過未收金額
document.getElementById('amount').addEventListener('input', function() {
    const remaining = parseFloat(document.getElementById('remainingAmount').textContent.replace(/,/g, ''));
    const amount = parseFloat(this.value);
    if (amount > remaining) { this.value = remaining; }
});

// 點擊 modal 外部關閉
document.getElementById('quickReceiveModal').addEventListener('click', function(e) {
    if (e.target === this) { closeQuickReceiveModal(); }
});
</script>
@endsection
