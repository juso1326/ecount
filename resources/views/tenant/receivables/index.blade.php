@extends('layouts.tenant')

@section('title', '應收帳款管理')

@section('page-title', '應收帳款管理')

@section('content')
<div class="mb-2 flex justify-end items-center">
    <a href="{{ route('tenant.receivables.create') }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增應收帳款
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-1 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- 智能搜尋 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-2">
    <form method="GET" action="{{ route('tenant.receivables.index') }}">
        <!-- 年度選擇器 -->
        <div class="flex items-center gap-3 mb-4">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">帳務年度：</label>
            <select name="fiscal_year" 
                    onchange="this.form.submit()"
                    class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">全部年度</option>
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ request('fiscal_year', date('Y')) == $year ? 'selected' : '' }}>
                        {{ $year }} 年
                    </option>
                @endforeach
            </select>
            @if(request('fiscal_year'))
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    目前顯示：<span class="font-semibold text-primary">{{ request('fiscal_year') }} 年度</span> 的應收帳款
                </span>
            @endif
        </div>

        <div class="flex gap-3">
            <div class="flex-1">
                <input type="text" 
                       name="smart_search" 
                       value="{{ request('smart_search') }}" 
                       placeholder="🔍 聰明尋找：找專案/找專案成員/找負責人/找發票號/報價單號..." 
                       class="w-full border-2 border-primary/30 dark:border-primary/50 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-1 text-base focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
            <select name="project_id" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">全部專案</option>
                @foreach(\App\Models\Project::where('is_active', true)->orderBy('code')->get() as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->code }} - {{ $project->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" 
                    class="bg-primary hover:bg-primary-dark text-white font-medium px-6 py-1 rounded-lg shadow-sm whitespace-nowrap">
                搜尋
            </button>
            @if(request('smart_search') || request('project_id') || request('fiscal_year') != date('Y'))
                <a href="{{ route('tenant.receivables.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium px-6 py-1 rounded-lg whitespace-nowrap">
                    清除
                </a>
            @endif
        </div>
        @if(request('smart_search'))
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                搜尋關鍵字：<span class="font-semibold text-primary">{{ request('smart_search') }}</span>
            </p>
        @endif
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">收款日期</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">單號</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">專案/客戶</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">內容</th>
                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">應收金額</th>
                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">已收金額</th>
                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">未收金額</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                <th class="px-3 py-1 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">操作</th>
            </tr>
            @if($receivables->count() > 0)
            <tr class="bg-blue-50 dark:bg-blue-900/30">
                <td colspan="4" class="px-6 py-2 text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                    總計（本頁）：
                </td>
                <td class="px-6 py-2 text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                    NT$ {{ number_format($totalAmount, 0) }}
                </td>
                <td class="px-6 py-2 text-right text-sm font-bold text-green-600 dark:text-green-400">
                    NT$ {{ number_format($totalReceived, 0) }}
                </td>
                <td class="px-6 py-2 text-right text-sm font-bold text-red-600 dark:text-red-400">
                    NT$ {{ number_format($totalAmount - $totalReceived, 0) }}
                </td>
                <td colspan="2"></td>
            </tr>
            @endif
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($receivables as $receivable)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-100">
                        {{ $receivable->receipt_date->format('Y/m/d') }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs font-medium text-gray-900 dark:text-gray-100">
                        {{ $receivable->receipt_no }}
                    </td>
                    <td class="px-4 py-2 text-xs text-gray-900 dark:text-gray-100">
                        <div class="font-medium">{{ $receivable->project?->code ?? '-' }}</div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">{{ $receivable->company?->name ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-2 text-xs text-gray-700 dark:text-gray-300 max-w-xs">
                        {{ Str::limit($receivable->content, 20) }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-right text-gray-900 dark:text-gray-100 font-medium">
                        NT$ {{ number_format($receivable->amount, 0) }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-right text-green-600 dark:text-green-400 font-medium">
                        NT$ {{ number_format($receivable->received_amount ?? 0, 0) }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-right {{ $receivable->remaining_amount > 0 ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-400' }}">
                        NT$ {{ number_format($receivable->remaining_amount, 0) }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-center">
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
                    <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium space-x-1">
                        <a href="{{ route('tenant.receivables.quick-receive', $receivable) }}" 
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">詳細</a>
                        <a href="{{ route('tenant.receivables.edit', $receivable) }}" 
                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">編輯</a>
                        
                        @if($receivable->status !== 'paid' && $receivable->remaining_amount > 0)
                            <button onclick="openQuickReceiveModal({{ $receivable->id }}, {{ $receivable->remaining_amount }}, '{{ $receivable->receipt_no }}')"
                                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                                收款
                            </button>
                        @endif
                        
                        <form action="{{ route('tenant.receivables.destroy', $receivable) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('確定要刪除此應收帳款嗎？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">刪除</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-1 text-center text-gray-500 dark:text-gray-400 text-sm">
                        目前沒有應收帳款資料
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 分頁 -->
<div class="mt-6">
    {{ $receivables->withQueryString()->links() }}
</div>

<!-- 快速收款 Modal -->
<div id="quickReceiveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal 標題 -->
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
                快速收款 - <span id="modalReceiptNo"></span>
            </h3>
            
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
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
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
                        <option value="現金">現金</option>
                        <option value="轉帳">轉帳</option>
                        <option value="支票">支票</option>
                        <option value="信用卡">信用卡</option>
                        <option value="其他">其他</option>
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
        </div>
    </div>
</div>

<script>
function openQuickReceiveModal(receivableId, remainingAmount, receiptNo) {
    document.getElementById('receivableId').value = receivableId;
    document.getElementById('amount').value = remainingAmount;
    document.getElementById('remainingAmount').textContent = new Intl.NumberFormat().format(remainingAmount);
    document.getElementById('modalReceiptNo').textContent = receiptNo;
    document.getElementById('quickReceiveModal').classList.remove('hidden');
    
    // 設定表單 action
    document.getElementById('quickReceiveForm').action = `/receivable-payments/${receivableId}`;
}

function closeQuickReceiveModal() {
    document.getElementById('quickReceiveModal').classList.add('hidden');
    document.getElementById('quickReceiveForm').reset();
}

// 驗證收款金額不超過未收金額
document.getElementById('amount').addEventListener('input', function() {
    const remaining = parseFloat(document.getElementById('remainingAmount').textContent.replace(/,/g, ''));
    const amount = parseFloat(this.value);
    
    if (amount > remaining) {
        this.value = remaining;
    }
});

// 點擊 modal 外部關閉
document.getElementById('quickReceiveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQuickReceiveModal();
    }
});
</script>
@endsection
