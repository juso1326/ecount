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
        
        <!-- 付款單號（隱藏，自動生成） -->
        <input type="hidden" name="payment_no" value="{{ old('payment_no', isset($payable) ? $payable->payment_no : $nextCode ?? '') }}">
        
        <!-- 類型（隱藏，預設為 expense） -->
        <input type="hidden" name="type" value="{{ old('type', isset($payable) ? $payable->type : 'expense') }}">

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

        <!-- 給付對象區塊 -->
        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">給付對象</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- 對象類型 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span class="text-red-500">*</span> 對象
                    </label>
                    <select name="payee_type" id="payee_type" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="">請選擇</option>
                        <option value="member" {{ old('payee_type', isset($payable) ? $payable->payee_type : '') == 'member' ? 'selected' : '' }}>成員</option>
                        <option value="vendor" {{ old('payee_type', isset($payable) ? $payable->payee_type : '') == 'vendor' ? 'selected' : '' }}>外製</option>
                        <option value="expense" {{ old('payee_type', isset($payable) ? $payable->payee_type : '') == 'expense' ? 'selected' : '' }}>已支出</option>
                    </select>
                </div>

                <!-- 成員選擇（payee_type = member 時顯示） -->
                <div id="member_field" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">成員</label>
                    <select name="payee_user_id" id="payee_user_id"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="">請選擇成員</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('payee_user_id', isset($payable) ? $payable->payee_user_id : '') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 廠商選擇（payee_type = vendor 時顯示） -->
                <div id="vendor_field" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">廠商</label>
                    <select name="payee_company_id" id="payee_company_id"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="">請選擇廠商</option>
                        @foreach($companies->where('is_outsource', true) as $company)
                            <option value="{{ $company->id }}" {{ old('payee_company_id', isset($payable) ? $payable->payee_company_id : '') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 支出資訊區塊（給付對象為外製或已支出時顯示） -->
        <div id="expense_info_section" class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">支出資訊</h3>
            <div class="space-y-4">
                <!-- 支出項目 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span class="text-red-500">*</span> 支出項目
                    </label>
                    <select name="expense_category_id" id="expense_category_id"
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
                    <textarea name="content" id="expense_content" rows="2"
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
                    <select name="payment_method" id="payment_method"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="">請選擇付款方式</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->name }}" {{ old('payment_method', isset($payable) ? $payable->payment_method : '') == $method->name ? 'selected' : '' }}>
                                {{ $method->name }}
                            </option>
                        @endforeach
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
    document.getElementById('manager_name').value = '請先選擇專案';
    document.getElementById('responsible_user_id').value = '';
});

// 專案變更時更新負責人
document.getElementById('project_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const managerId = selectedOption.getAttribute('data-manager-id');
    
    if (managerId) {
        // 從 users 陣列找出對應的負責人名稱
        const users = @json($users);
        const manager = users.find(u => u.id == managerId);
        
        if (manager) {
            document.getElementById('manager_name').value = manager.name;
            document.getElementById('responsible_user_id').value = manager.id;
        }
    } else {
        document.getElementById('manager_name').value = '請先選擇專案';
        document.getElementById('responsible_user_id').value = '';
    }
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

// 給付對象類型切換
document.getElementById('payee_type').addEventListener('change', function() {
    const payeeType = this.value;
    const memberField = document.getElementById('member_field');
    const vendorField = document.getElementById('vendor_field');
    const expenseSection = document.getElementById('expense_info_section');
    const memberSelect = document.getElementById('payee_user_id');
    const vendorSelect = document.getElementById('payee_company_id');
    const expenseCategorySelect = document.getElementById('expense_category_id');
    const expenseContent = document.getElementById('expense_content');
    
    // 隱藏所有欄位
    memberField.style.display = 'none';
    vendorField.style.display = 'none';
    
    // 清空欄位
    memberSelect.value = '';
    vendorSelect.value = '';
    
    // 根據類型顯示對應欄位
    if (payeeType === 'member') {
        memberField.style.display = 'block';
        // 隱藏支出資訊區塊（成員不需要）
        expenseSection.style.display = 'none';
        expenseCategorySelect.removeAttribute('required');
        expenseContent.removeAttribute('required');
    } else if (payeeType === 'vendor') {
        vendorField.style.display = 'block';
        // 顯示支出資訊區塊
        expenseSection.style.display = 'block';
        expenseCategorySelect.setAttribute('required', 'required');
        expenseContent.setAttribute('required', 'required');
    } else if (payeeType === 'expense') {
        // 已支出也顯示支出資訊區塊
        expenseSection.style.display = 'block';
        expenseCategorySelect.setAttribute('required', 'required');
        expenseContent.setAttribute('required', 'required');
    }
});

// 頁面載入時根據已選值顯示對應欄位
document.addEventListener('DOMContentLoaded', function() {
    const payeeType = document.getElementById('payee_type').value;
    const expenseSection = document.getElementById('expense_info_section');
    const expenseCategorySelect = document.getElementById('expense_category_id');
    const expenseContent = document.getElementById('expense_content');
    
    if (payeeType === 'member') {
        document.getElementById('member_field').style.display = 'block';
        expenseSection.style.display = 'none';
        expenseCategorySelect.removeAttribute('required');
        expenseContent.removeAttribute('required');
    } else if (payeeType === 'vendor') {
        document.getElementById('vendor_field').style.display = 'block';
        expenseSection.style.display = 'block';
    } else if (payeeType === 'expense') {
        expenseSection.style.display = 'block';
    }
});

// Initialize Select2 for project dropdown
$(document).ready(function() {
    $('#project_id').select2({
        placeholder: '請選擇專案',
        allowClear: true,
        width: '100%'
    });
    
    // Re-bind change event after Select2 initialization
    $('#project_id').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const managerId = selectedOption.attr('data-manager-id');
        
        if (managerId) {
            const users = @json($users);
            const manager = users.find(u => u.id == managerId);
            
            if (manager) {
                $('#manager_name').val(manager.name);
                $('#responsible_user_id').val(manager.id);
            }
        } else {
            $('#manager_name').val('請先選擇專案');
            $('#responsible_user_id').val('');
        }
    });
    
    // Update company filter to work with Select2
    $('#company_id').on('change', function() {
        const companyId = $(this).val();
        
        // Clear selection
        $('#project_id').val('').trigger('change');
        $('#manager_name').val('請先選擇專案');
        $('#responsible_user_id').val('');
        
        // Filter options
        $('#project_id option').each(function() {
            const optionCompanyId = $(this).attr('data-company-id');
            if (!$(this).val() || !companyId || optionCompanyId === companyId) {
                $(this).prop('disabled', false);
            } else {
                $(this).prop('disabled', true);
            }
        });
        
        // Refresh Select2 to reflect disabled options
        $('#project_id').trigger('change.select2');
    });
});

</script>
@endpush
