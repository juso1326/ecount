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
    <form action="{{ isset($payable) ? route('tenant.payables.update', $payable) : route('tenant.payables.store') }}" method="POST" class="p-6">
        @csrf
        @if(isset($payable))
            @method('PUT')
        @endif

        <!-- 專案資訊區塊 -->
        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">專案</h3>
            <div class="space-y-4">
                <!-- 客戶 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">客戶</label>
                    <select name="company_id" id="company_id"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="">請選擇客戶</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" 
                                    data-tax-id="{{ $company->tax_id ?? '' }}"
                                    {{ old('company_id', isset($payable) ? $payable->company_id : '') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 專案 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">專案</label>
                    <select name="project_id" id="project_id"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="">請選擇專案</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" 
                                    data-company-id="{{ $project->company_id }}"
                                    data-manager-id="{{ $project->responsible_user_id }}"
                                    {{ old('project_id', isset($payable) ? $payable->project_id : '') == $project->id ? 'selected' : '' }}>
                                {{ $project->code }} - {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- 負責人（自動帶入） -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">負責人</label>
                    <input type="text" id="manager_name" disabled
                           class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-600 dark:text-white rounded-lg px-4 py-2"
                           value="{{ isset($payable) && $payable->project ? $payable->project->responsibleUser->name : '請先選擇專案' }}">
                    <input type="hidden" name="responsible_user_id" id="responsible_user_id" 
                           value="{{ old('responsible_user_id', isset($payable) ? $payable->responsible_user_id : '') }}">
                </div>
            </div>
        </div>

        <!-- 廠商資訊區塊 -->
        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">廠商資訊</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- 廠商名稱 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span class="text-red-500">*</span> 廠商名稱
                    </label>
                    <input type="text" name="vendor_name" required
                           value="{{ old('vendor_name', isset($payable) ? $payable->vendor_name : '') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                </div>

                <!-- 廠商統編 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">廠商統編</label>
                    <input type="text" name="vendor_tax_id"
                           value="{{ old('vendor_tax_id', isset($payable) ? $payable->vendor_tax_id : '') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                </div>

                <!-- 廠商電話 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">廠商電話</label>
                    <input type="text" name="vendor_phone"
                           value="{{ old('vendor_phone', isset($payable) ? $payable->vendor_phone : '') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                </div>

                <!-- 廠商聯絡人 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">廠商聯絡人</label>
                    <input type="text" name="vendor_contact"
                           value="{{ old('vendor_contact', isset($payable) ? $payable->vendor_contact : '') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                </div>

                <!-- 廠商地址 -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">廠商地址</label>
                    <input type="text" name="vendor_address"
                           value="{{ old('vendor_address', isset($payable) ? $payable->vendor_address : '') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                </div>
            </div>
        </div>

        <!-- 支出資訊區塊 -->
        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">支出資訊</h3>
            <div class="space-y-4">
                <!-- 支出項目 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span class="text-red-500">*</span> 支出項目
                    </label>
                    <select name="expense_category_id" id="expense_category_id" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="">請選擇支出項目</option>
                        @foreach($expenseCategories as $category)
                            @if($category->parent_id === null)
                                <optgroup label="{{ $category->name }}">
                                    @foreach($expenseCategories->where('parent_id', $category->id) as $subCategory)
                                        <option value="{{ $subCategory->id }}" 
                                                {{ old('expense_category_id', isset($payable) ? $payable->expense_category_id : '') == $subCategory->id ? 'selected' : '' }}>
                                            {{ $subCategory->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- 內容說明 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span class="text-red-500">*</span> 內容說明
                    </label>
                    <textarea name="content" rows="2" required
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">{{ old('content', isset($payable) ? $payable->content : '') }}</textarea>
                </div>

                <!-- 金額 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span class="text-red-500">*</span> 金額
                    </label>
                    <input type="number" name="amount_before_tax" id="amount_before_tax" 
                           value="{{ old('amount_before_tax', isset($payable) ? $payable->amount_before_tax : 0) }}" 
                           step="1" min="0" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                </div>

                <!-- 稅款設定 -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">稅款</label>
                        <select name="tax_setting_id" id="tax_setting_id"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                            <option value="" data-rate="0">無</option>
                            @foreach($taxSettings as $tax)
                                <option value="{{ $tax->id }}" 
                                        data-rate="{{ $tax->rate }}"
                                        {{ old('tax_setting_id', isset($payable) ? $payable->tax_setting_id : '') == $tax->id ? 'selected' : '' }}>
                                    {{ $tax->name }} ({{ $tax->rate }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">稅額計算</label>
                        <div class="flex gap-4 pt-2">
                            <label class="flex items-center">
                                <input type="radio" name="tax_inclusive" id="tax_inclusive_0" value="0" 
                                       {{ old('tax_inclusive', isset($payable) ? $payable->tax_inclusive : 0) == 0 ? 'checked' : '' }}
                                       class="mr-2">
                                <span>外加</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="tax_inclusive" id="tax_inclusive_1" value="1"
                                       {{ old('tax_inclusive', isset($payable) ? $payable->tax_inclusive : 0) == 1 ? 'checked' : '' }}
                                       class="mr-2">
                                <span>內含</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 總計 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">總計</label>
                    <input type="number" name="amount" id="amount" 
                           value="{{ old('amount', isset($payable) ? $payable->amount : 0) }}" 
                           step="1" min="0" readonly
                           class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2 font-bold text-lg">
                    <input type="hidden" name="tax_amount" id="tax_amount" value="0">
                </div>

                <!-- 備註 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">備註</label>
                    <textarea name="note" rows="2"
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"></textarea>
                </div>
            </div>
        </div>

        <!-- 支出明細區塊 -->
        <div class="mb-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">支出明細</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- 付款日期 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">付款日期</label>
                    <input type="date" name="payment_date" 
                           value="{{ old('payment_date', isset($payable) ? $payable->payment_date : now()->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                </div>

                <!-- 發票號碼 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">發票號碼</label>
                    <input type="text" name="invoice_no"
                           value="{{ old('invoice_no', isset($payable) ? $payable->invoice_no : '') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"
                           placeholder="例如：AB12345678">
                </div>

                <!-- 預計付款日 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">預計付款日</label>
                    <input type="date" name="due_date"
                           value="{{ old('due_date', isset($payable) ? $payable->due_date : '') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                </div>

                <!-- 付款方式 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">付款方式</label>
                    <select name="payment_method"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="">請選擇</option>
                        <option value="轉帳匯款" {{ old('payment_method', isset($payable) ? $payable->payment_method : '') == '轉帳匯款' ? 'selected' : '' }}>轉帳匯款</option>
                        <option value="現金" {{ old('payment_method', isset($payable) ? $payable->payment_method : '') == '現金' ? 'selected' : '' }}>現金</option>
                        <option value="支票" {{ old('payment_method', isset($payable) ? $payable->payment_method : '') == '支票' ? 'selected' : '' }}>支票</option>
                        <option value="信用卡" {{ old('payment_method', isset($payable) ? $payable->payment_method : '') == '信用卡' ? 'selected' : '' }}>信用卡</option>
                        <option value="其他" {{ old('payment_method', isset($payable) ? $payable->payment_method : '') == '其他' ? 'selected' : '' }}>其他</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 按鈕 -->
        <div class="flex gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
                {{ isset($payable) ? '更新' : '新增' }}
            </button>
            <a href="{{ route('tenant.payables.index') }}"
               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                取消
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
// 客戶變更時篩選專案
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
    
    projectSelect.value = '';
});

// 稅額計算
function calculateTax() {
    const amountBeforeTax = parseFloat(document.getElementById('amount_before_tax').value) || 0;
    const taxSelect = document.getElementById('tax_setting_id');
    const taxRate = parseFloat(taxSelect.options[taxSelect.selectedIndex].getAttribute('data-rate')) || 0;
    const taxInclusive = document.querySelector('input[name="tax_inclusive"]:checked').value == '1';
    
    let taxAmount = 0;
    let totalAmount = 0;
    
    if (taxInclusive) {
        totalAmount = amountBeforeTax;
        taxAmount = Math.round(amountBeforeTax * taxRate / (100 + taxRate));
    } else {
        taxAmount = Math.round(amountBeforeTax * taxRate / 100);
        totalAmount = amountBeforeTax + taxAmount;
    }
    
    document.getElementById('tax_amount').value = taxAmount;
    document.getElementById('amount').value = totalAmount;
}

document.getElementById('amount_before_tax').addEventListener('input', calculateTax);
document.getElementById('tax_setting_id').addEventListener('change', calculateTax);
document.querySelectorAll('input[name="tax_inclusive"]').forEach(radio => {
    radio.addEventListener('change', calculateTax);
});

document.addEventListener('DOMContentLoaded', calculateTax);
</script>
@endpush
