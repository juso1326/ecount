@extends('layouts.tenant')

@section('title', '新增應收帳款')

@section('page-title', '新增應收帳款')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.receivables.index') }}" class="hover:text-primary">財務管理</a> &gt; 
        <a href="{{ route('tenant.receivables.index') }}" class="hover:text-primary">應收帳款管理</a> &gt; 
        新增
    </p>
</div>

<!-- 頁面標題 -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">新增應收帳款</h1>
</div>

@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- 表單 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <form action="{{ route('tenant.receivables.store') }}" method="POST" class="p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 收款單號 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    收款單號 <span class="text-gray-500">(自動)</span>
                </label>
                <input type="text" name="receipt_no" value="{{ $nextCode }}" readonly
                       class="w-full border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-lg px-4 py-2 cursor-not-allowed">
                <p class="mt-1 text-xs text-gray-500">系統將自動產生收款單號</p>
            </div>

            <!-- 收款日期 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    收款日期 <span class="text-red-500">*</span>
                </label>
                <input type="date" name="receipt_date" value="{{ old('receipt_date', now()->format('Y-m-d')) }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <!-- 客戶 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">客戶</label>
                <select name="company_id" id="company_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">請選擇客戶</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 專案 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">專案</label>
                <select name="project_id" id="project_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">請選擇專案</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" 
                                data-company-id="{{ $project->company_id }}"
                                data-manager-id="{{ $project->manager_id }}"
                                {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 負責人 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">負責人（收款負責人）</label>
                <select name="responsible_user_id" id="responsible_user_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">請選擇負責人</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('responsible_user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 到期日 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">到期日</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <!-- 應收金額 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    應收金額 <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" id="amount" value="{{ old('amount', isset($receivable) ? $receivable->amount : 0) }}" step="1" min="0" required readonly
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent bg-gray-50"
                       placeholder="0">
                <p class="text-xs text-gray-500 mt-1">此金額由「未稅金額」和「稅款設定」自動計算</p>
            </div>

            <!-- 未稅金額 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    未稅金額 <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount_before_tax" id="amount_before_tax" value="{{ old('amount_before_tax', isset($receivable) ? $receivable->amount_before_tax : 0) }}" step="1" min="0" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="0">
            </div>

            <!-- 稅款設定 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">稅款設定</label>
                <select name="tax_setting_id" id="tax_setting_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="" data-rate="0">無</option>
                    @foreach($taxSettings as $tax)
                        <option value="{{ $tax->id }}" 
                                data-rate="{{ $tax->rate }}"
                                {{ old('tax_setting_id', isset($receivable) ? $receivable->tax_setting_id : '') == $tax->id ? 'selected' : '' }}>
                            {{ $tax->name }} ({{ $tax->rate }}%)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 稅額計算方式 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">稅額計算</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="tax_inclusive" id="tax_inclusive_0" value="0" 
                               {{ old('tax_inclusive', isset($receivable) ? $receivable->tax_inclusive : 0) == 0 ? 'checked' : '' }}
                               class="mr-2">
                        <span>外加</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="tax_inclusive" id="tax_inclusive_1" value="1"
                               {{ old('tax_inclusive', isset($receivable) ? $receivable->tax_inclusive : 0) == 1 ? 'checked' : '' }}
                               class="mr-2">
                        <span>內含</span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-1">外加：未稅金額 + 稅額 = 應收金額｜內含：未稅金額 = 應收金額（已含稅）</p>
            </div>

            <!-- 稅額顯示 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">稅額</label>
                <input type="number" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', isset($receivable) ? $receivable->tax_amount : 0) }}" step="1" min="0" readonly
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 bg-gray-50"
                       placeholder="0">
            </div>

            <!-- 扣繳稅額 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">扣繳稅額</label>
                <input type="number" name="withholding_tax" value="{{ old('withholding_tax', 0) }}" step="1" min="0"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="0">
            </div>

            <!-- 已收金額 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">已收金額</label>
                <input type="number" name="received_amount" value="{{ old('received_amount', 0) }}" step="1" min="0"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="0">
            </div>

            <!-- 狀態 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    狀態 <span class="text-red-500">*</span>
                </label>
                <select name="status" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>待收款</option>
                    <option value="partial" {{ old('status') == 'partial' ? 'selected' : '' }}>部分收款</option>
                    <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>已收款</option>
                    <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>逾期</option>
                </select>
            </div>

            <!-- 付款方式 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">付款方式</label>
                <select name="payment_method"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">請選擇</option>
                    <option value="轉帳匯款" {{ old('payment_method') == '轉帳匯款' ? 'selected' : '' }}>轉帳匯款</option>
                    <option value="現金" {{ old('payment_method') == '現金' ? 'selected' : '' }}>現金</option>
                    <option value="支票" {{ old('payment_method') == '支票' ? 'selected' : '' }}>支票</option>
                    <option value="信用卡" {{ old('payment_method') == '信用卡' ? 'selected' : '' }}>信用卡</option>
                    <option value="其他" {{ old('payment_method') == '其他' ? 'selected' : '' }}>其他</option>
                </select>
            </div>

            <!-- 實際收款日 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">實際收款日</label>
                <input type="date" name="paid_date" value="{{ old('paid_date') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <!-- 發票號碼 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">發票號碼</label>
                <input type="text" name="invoice_no" value="{{ old('invoice_no') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="例如：AB12345678">
            </div>

            <!-- 報價單號 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">報價單號</label>
                <input type="text" name="quote_no" value="{{ old('quote_no') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="例如：Q202602001">
            </div>

            <!-- 內容 -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">內容</label>
                <textarea name="content" rows="3"
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                          placeholder="收款內容說明">{{ old('content') }}</textarea>
            </div>

            <!-- 備註 -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">備註</label>
                <textarea name="note" rows="2"
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                          placeholder="其他備註">{{ old('note') }}</textarea>
            </div>
        </div>

        <!-- 按鈕 -->
        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
                新增
            </button>
            <a href="{{ route('tenant.receivables.index') }}"
               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                取消
            </a>
        </div>
    </form>
</div>

<script>
// 當選擇客戶時，篩選對應的專案
document.getElementById('company_id').addEventListener('change', function() {
    const companyId = this.value;
    const projectSelect = document.getElementById('project_id');
    const projectOptions = projectSelect.querySelectorAll('option');
    
    projectOptions.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const optionCompanyId = option.getAttribute('data-company-id');
        
        if (!companyId || optionCompanyId === companyId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    // 重置專案選擇
    projectSelect.value = '';
    document.getElementById('responsible_user_id').value = '';
});

// 當選擇專案時，自動帶入負責人（專案經理）
document.getElementById('project_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const managerId = selectedOption.getAttribute('data-manager-id');
    const responsibleSelect = document.getElementById('responsible_user_id');
    
    if (managerId) {
        responsibleSelect.value = managerId;
    }
});

// 稅額計算功能
function calculateTax() {
    const amountBeforeTax = parseFloat(document.getElementById('amount_before_tax').value) || 0;
    const taxSelect = document.getElementById('tax_setting_id');
    const taxRate = parseFloat(taxSelect.options[taxSelect.selectedIndex].getAttribute('data-rate')) || 0;
    const taxInclusive = document.querySelector('input[name="tax_inclusive"]:checked').value == '1';
    
    let taxAmount = 0;
    let totalAmount = 0;
    
    if (taxInclusive) {
        // 內含：未稅金額已經是含稅價，需要反推稅額
        totalAmount = amountBeforeTax;
        taxAmount = Math.round(amountBeforeTax * taxRate / (100 + taxRate));
    } else {
        // 外加：稅額 = 未稅金額 * 稅率
        taxAmount = Math.round(amountBeforeTax * taxRate / 100);
        totalAmount = amountBeforeTax + taxAmount;
    }
    
    document.getElementById('tax_amount').value = taxAmount;
    document.getElementById('amount').value = totalAmount;
}

// 監聽變更事件
document.getElementById('amount_before_tax').addEventListener('input', calculateTax);
document.getElementById('tax_setting_id').addEventListener('change', calculateTax);
document.querySelectorAll('input[name="tax_inclusive"]').forEach(radio => {
    radio.addEventListener('change', calculateTax);
});

// 初始計算一次
document.addEventListener('DOMContentLoaded', calculateTax);
</script>
@endsection
