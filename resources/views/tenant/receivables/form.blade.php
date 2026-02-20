@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-2">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- 表單 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <form action="{{ isset($receivable) ? route('tenant.receivables.update', $receivable) : route('tenant.receivables.store') }}" method="POST" class="p-6">
        @csrf
        @if(isset($receivable))
            @method('PUT')
        @endif

        <div class="space-y-2">
            <!-- 客戶 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <span class="text-red-500">*</span> 客戶
                </label>
                <select name="company_id" id="company_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent select2-company">
                    <option value="">請選擇客戶</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" 
                                data-tax-id="{{ $company->tax_id ?? '' }}"
                                {{ old('company_id', isset($receivable) ? $receivable->company_id : '') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 統編（顯示） -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">統編</label>
                <div id="taxIdDisplay" class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg border border-gray-300 dark:border-gray-600">
                    -
                </div>
            </div>

            <!-- 專案 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">專案</label>
                <select name="project_id" id="project_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent select2-project">
                    <option value="">請選擇專案</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" 
                                data-company-id="{{ $project->company_id }}"
                                data-manager-id="{{ $project->manager_id }}"
                                {{ old('project_id', isset($receivable) ? $receivable->project_id : '') == $project->id ? 'selected' : '' }}>
                            {{ $project->code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 內容 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">內容</label>
                <textarea name="content" rows="2"
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                          placeholder="收款內容說明">{{ old('content', isset($receivable) ? $receivable->content : '') }}</textarea>
            </div>

            <!-- 金額 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <span class="text-red-500">*</span> 金額
                </label>
                <input type="number" name="amount_before_tax" id="amount_before_tax" 
                       value="{{ old('amount_before_tax', isset($receivable) ? $receivable->amount_before_tax : 0) }}" 
                       step="1" min="0" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <!-- 稅款設定 -->
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">稅款</label>
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
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">稅額計算</label>
                    <div class="flex gap-2 pt-2">
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
                </div>
            </div>

            <!-- 總計（顯示） -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">總計</label>
                <input type="number" name="amount" id="amount" 
                       value="{{ old('amount', isset($receivable) ? $receivable->amount : 0) }}" 
                       step="1" min="0" readonly
                       class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2 font-bold text-lg">
                <input type="hidden" name="tax_amount" id="tax_amount" value="0">
            </div>

            <!-- 備註 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">備註</label>
                <textarea name="note" rows="2"
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('note', isset($receivable) ? $receivable->note : '') }}</textarea>
            </div>

            <!-- 開立資訊 -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">開立資訊</h3>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">日期</label>
                        <input type="date" name="receipt_date" id="receipt_date"
                               value="{{ old('receipt_date', isset($receivable) ? $receivable->receipt_date : now()->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">帳務年度</label>
                        <input type="number" name="fiscal_year" id="fiscal_year"
                               value="{{ old('fiscal_year', isset($receivable) ? $receivable->fiscal_year : date('Y')) }}"
                               min="2000" max="2100"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="YYYY">
                        <p class="text-xs text-gray-500 dark:text-gray-400">可手動調整年度</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">發票號碼</label>
                        <input type="text" name="invoice_no" 
                               value="{{ old('invoice_no', isset($receivable) ? $receivable->invoice_no : '') }}"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="例如：AB12345678">
                    </div>
                </div>
            </div>

            <!-- 隱藏欄位（從專案自動帶入） -->
            <input type="hidden" name="responsible_user_id" id="responsible_user_id" value="{{ old('responsible_user_id', isset($receivable) ? $receivable->responsible_user_id : auth()->id()) }}">
            <input type="hidden" name="status" value="{{ old('status', isset($receivable) ? $receivable->status : 'unpaid') }}">
        </div>

        <!-- 按鈕 -->
        <div class="flex gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
                {{ isset($receivable) ? '更新' : '新增' }}
            </button>
            <a href="{{ route('tenant.receivables.index') }}"
               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                取消
            </a>
        </div>
    </form>
</div>

@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // 初始化 Select2
    $('.select2-company').select2({
        placeholder: '請搜尋客戶',
        width: '100%',
        allowClear: true
    });
    
    $('.select2-project').select2({
        placeholder: '請搜尋專案',
        width: '100%',
        allowClear: true
    });
});

// 當選擇客戶時，顯示統編並篩選對應的專案
document.getElementById('company_id').addEventListener('change', function() {
    const companyId = this.value;
    const selectedOption = this.options[this.selectedIndex];
    const taxId = selectedOption.getAttribute('data-tax-id') || '-';
    
    // 顯示統編
    document.getElementById('taxIdDisplay').textContent = taxId;
    
    // 篩選專案
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
    $('#project_id').val(null).trigger('change');
    document.getElementById('responsible_user_id').value = '{{ auth()->id() }}';
});

// 當選擇專案時，自動帶入負責人（專案經理）
document.getElementById('project_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const managerId = selectedOption.getAttribute('data-manager-id');
    
    if (managerId) {
        document.getElementById('responsible_user_id').value = managerId;
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

// 日期變更時自動更新年度
document.getElementById('receipt_date').addEventListener('change', function() {
    const dateValue = this.value;
    if (dateValue) {
        const year = dateValue.split('-')[0];
        document.getElementById('fiscal_year').value = year;
    }
});

// 初始化：顯示已選客戶的統編
document.addEventListener('DOMContentLoaded', function() {
    const companySelect = document.getElementById('company_id');
    if (companySelect.value) {
        const selectedOption = companySelect.options[companySelect.selectedIndex];
        const taxId = selectedOption.getAttribute('data-tax-id') || '-';
        document.getElementById('taxIdDisplay').textContent = taxId;
    }
    calculateTax();
});

// 初始化 Select2
$(document).ready(function() {
    // 客戶選擇 Select2
    $('#company_id').select2({
        placeholder: '請選擇客戶',
        allowClear: false,
        width: '100%'
    });

    // 專案選擇 Select2
    $('#project_id').select2({
        placeholder: '請選擇專案',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
