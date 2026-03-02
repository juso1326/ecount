@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <form action="{{ isset($payable) ? route('tenant.payables.update', $payable) : route('tenant.payables.store') }}" method="POST" class="p-6 space-y-4">
        @csrf
        @if(isset($payable))
            @method('PUT')
        @endif

        <input type="hidden" name="payment_no" value="{{ old('payment_no', isset($payable) ? $payable->payment_no : $nextCode ?? '') }}">
        <input type="hidden" name="type" value="{{ old('type', isset($payable) ? $payable->type : 'expense') }}">
        <input type="hidden" name="responsible_user_id" id="responsible_user_id"
               value="{{ old('responsible_user_id', isset($payable) ? $payable->responsible_user_id : '') }}">

        {{-- 第一行：客戶 + 專案 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">客戶</label>
                <select name="company_id" id="company_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="">請選擇客戶</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}"
                                {{ old('company_id', isset($payable) ? $payable->company_id : '') == $company->id ? 'selected' : '' }}>
                            {{ $company->short_name ?: $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">專案</label>
                <select name="project_id" id="project_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
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
        </div>

        <hr class="border-gray-200 dark:border-gray-700">

        {{-- 第二行：對象類型 + 成員/廠商 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <span class="text-red-500">*</span> 給付對象
                </label>
                <select name="payee_type" id="payee_type" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="">請選擇</option>
                    <option value="member" {{ old('payee_type', isset($payable) ? $payable->payee_type : '') == 'member' ? 'selected' : '' }}>成員</option>
                    <option value="vendor" {{ old('payee_type', isset($payable) ? $payable->payee_type : '') == 'vendor' ? 'selected' : '' }}>外包</option>
                    <option value="expense" {{ old('payee_type', isset($payable) ? $payable->payee_type : '') == 'expense' ? 'selected' : '' }}>採購</option>
                </select>
            </div>

            {{-- 成員 --}}
            <div id="member_field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">成員</label>
                <select name="payee_user_id" id="payee_user_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="">請選擇成員</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('payee_user_id', isset($payable) ? $payable->payee_user_id : '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 廠商 --}}
            <div id="vendor_field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">廠商</label>
                <select name="payee_company_id" id="payee_company_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="">請選擇廠商</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('payee_company_id', isset($payable) ? $payable->payee_company_id : '') == $company->id ? 'selected' : '' }}>{{ $company->short_name ?: $company->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- 採購欄位 --}}
        <div id="expense_company_fields" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">公司名稱</label>
                <input type="text" name="expense_company_name" id="expense_company_name"
                       value="{{ old('expense_company_name', isset($payable) ? $payable->expense_company_name : '') }}"
                       placeholder="採購對象名稱"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">統一編號</label>
                <input type="text" name="expense_tax_id" id="expense_tax_id"
                       value="{{ old('expense_tax_id', isset($payable) ? $payable->expense_tax_id : '') }}"
                       placeholder="統編（選填）"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer">
                    <input type="checkbox" id="has_advance" class="rounded text-primary"
                           onchange="toggleAdvanceUser()"
                           {{ old('advance_user_id', isset($payable) ? $payable->advance_user_id : '') ? 'checked' : '' }}>
                    成員代墊
                </label>
                <select name="advance_user_id" id="advance_user_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm {{ old('advance_user_id', isset($payable) ? $payable->advance_user_id : '') ? '' : 'hidden' }}">
                    <option value="">請選擇代墊成員</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('advance_user_id', isset($payable) ? $payable->advance_user_id : '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr class="border-gray-200 dark:border-gray-700">

        {{-- 支出項目 + 內容 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">支出項目</label>
                <select name="expense_category_id" id="expense_category_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="">請選擇支出項目</option>
                    @foreach($expenseCategories as $category)
                        @if($category->parent_id === null)
                            <optgroup label="{{ $category->name }}">
                                @foreach($expenseCategories->where('parent_id', $category->id) as $sub)
                                    <option value="{{ $sub->id }}" {{ old('expense_category_id', isset($payable) ? $payable->expense_category_id : '') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <span class="text-red-500">*</span> 支付內容
                </label>
                <input type="text" name="content"
                       value="{{ old('content', isset($payable) ? $payable->content : '') }}"
                       required placeholder="輸入支付內容說明"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        {{-- 金額 + 稅款 + 計算方式 + 總計 --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <span class="text-red-500">*</span> 未稅金額
                </label>
                <input type="number" name="amount_before_tax" id="amount_before_tax"
                       value="{{ old('amount_before_tax', isset($payable) ? $payable->amount_before_tax : 0) }}"
                       step="any" min="0" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">稅款</label>
                <select name="tax_setting_id" id="tax_setting_id"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="" data-rate="0">無</option>
                    @foreach($taxSettings as $tax)
                        <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}"
                                {{ old('tax_setting_id', isset($payable) ? $payable->tax_setting_id : '') == $tax->id ? 'selected' : '' }}>
                            {{ $tax->name }} ({{ $tax->rate }}%)
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">稅額計算</label>
                <div class="flex gap-4 py-2">
                    <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                        <input type="radio" name="tax_inclusive" value="0" class="text-primary"
                               {{ old('tax_inclusive', isset($payable) ? $payable->tax_inclusive : 0) == 0 ? 'checked' : '' }}>
                        外加
                    </label>
                    <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                        <input type="radio" name="tax_inclusive" value="1" class="text-primary"
                               {{ old('tax_inclusive', isset($payable) ? $payable->tax_inclusive : 0) == 1 ? 'checked' : '' }}>
                        內含
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">應付總計</label>
                <input type="number" name="amount" id="amount"
                       value="{{ old('amount', isset($payable) ? $payable->amount : 0) }}"
                       step="any" min="0" readonly
                       class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 font-semibold text-sm">
                <input type="hidden" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', isset($payable) ? $payable->tax_amount : 0) }}">
            </div>
        </div>

        <hr class="border-gray-200 dark:border-gray-700">

        {{-- 付款方式 + 發票日期 + 發票號碼 --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">付款方式</label>
                <select name="payment_method" id="payment_method"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    <option value="">請選擇</option>
                    @foreach($paymentMethods as $method)
                        <option value="{{ $method->name }}" {{ old('payment_method', isset($payable) ? $payable->payment_method : '') == $method->name ? 'selected' : '' }}>{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">發票日期</label>
                <input type="date" name="invoice_date"
                       value="{{ old('invoice_date', isset($payable) ? ($payable->invoice_date ? $payable->invoice_date->format('Y-m-d') : '') : '') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">發票號碼</label>
                <input type="text" name="invoice_no"
                       value="{{ old('invoice_no', isset($payable) ? $payable->invoice_no : '') }}"
                       placeholder="例如：AB12345678"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        {{-- 預計付款日 + 付款日期 + 帳務年度 --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">預計付款日</label>
                <input type="date" name="due_date"
                       value="{{ old('due_date', isset($payable) ? $payable->due_date : '') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">付款日期</label>
                <input type="date" name="payment_date" id="payment_date"
                       value="{{ old('payment_date', isset($payable) ? $payable->payment_date : now()->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">帳務年度</label>
                <input type="number" name="fiscal_year" id="fiscal_year"
                       value="{{ old('fiscal_year', isset($payable) ? $payable->fiscal_year : date('Y')) }}"
                       min="2000" max="2100" placeholder="YYYY"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        {{-- 備註 --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">備註</label>
            <textarea name="note" rows="2"
                      class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">{{ old('note', isset($payable) ? $payable->note : '') }}</textarea>
        </div>

        {{-- 按鈕 --}}
        <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
                {{ isset($payable) ? '更新' : '新增' }}
            </button>
            <a href="{{ route('tenant.payables.index') }}"
               class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                取消
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Select2 ──────────────────────────────────────────────────────────
    $('#company_id, #project_id, #payee_user_id, #payee_company_id').select2({ allowClear: true, width: '100%' });

    // ── 給付對象切換 ──────────────────────────────────────────────────────
    function updatePayeeFields(type) {
        document.getElementById('member_field').classList.toggle('hidden', type !== 'member');
        document.getElementById('vendor_field').classList.toggle('hidden', type !== 'vendor');
        const expFields = document.getElementById('expense_company_fields');
        if (type === 'expense') {
            expFields.classList.remove('hidden');
            expFields.style.display = 'grid';
        } else {
            expFields.classList.add('hidden');
            expFields.style.display = '';
        }
    }

    document.getElementById('payee_type').addEventListener('change', function () {
        updatePayeeFields(this.value);
    });

    // 初始化
    updatePayeeFields(document.getElementById('payee_type').value);

    // ── 客戶變更篩選專案 ──────────────────────────────────────────────────
    $('#company_id').on('change', function () {
        const companyId = $(this).val();
        $('#project_id option').each(function () {
            const opt = $(this);
            const optCo = opt.attr('data-company-id');
            opt.prop('disabled', companyId && opt.val() && optCo !== companyId);
        });
        $('#project_id').val('').trigger('change.select2');
        $('#responsible_user_id').val('');
    });

    // ── 專案變更帶入負責人 ────────────────────────────────────────────────
    $('#project_id').on('change', function () {
        const managerId = $(this).find(':selected').attr('data-manager-id');
        $('#responsible_user_id').val(managerId || '');
    });

    // ── 稅額計算 ──────────────────────────────────────────────────────────
    function calculateTax() {
        const before = parseFloat(document.getElementById('amount_before_tax').value) || 0;
        const taxSel = document.getElementById('tax_setting_id');
        const rate = parseFloat(taxSel.options[taxSel.selectedIndex].getAttribute('data-rate')) || 0;
        const inclusive = document.querySelector('input[name="tax_inclusive"]:checked').value === '1';

        let tax = 0, total = 0;
        if (inclusive) {
            total = before;
            tax   = Math.round(before * rate / (100 + rate) * 100) / 100;
        } else {
            tax   = Math.round(before * rate / 100 * 100) / 100;
            total = before + tax;
        }
        document.getElementById('tax_amount').value = tax;
        document.getElementById('amount').value = total;
    }

    document.getElementById('amount_before_tax').addEventListener('input', calculateTax);
    document.getElementById('tax_setting_id').addEventListener('change', calculateTax);
    document.querySelectorAll('input[name="tax_inclusive"]').forEach(r => r.addEventListener('change', calculateTax));

    @if(!isset($payable))
    calculateTax();
    @endif

    // ── 付款日期 → 帳務年度 ───────────────────────────────────────────────
    document.getElementById('payment_date').addEventListener('change', function () {
        const y = this.value.split('-')[0];
        if (y) document.getElementById('fiscal_year').value = y;
    });
});

function toggleAdvanceUser() {
    const sel = document.getElementById('advance_user_id');
    const checked = document.getElementById('has_advance').checked;
    sel.classList.toggle('hidden', !checked);
    if (!checked) sel.value = '';
}
</script>
@endpush
