<!-- 基本資訊 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">基本資訊</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 公司代碼 -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    公司代碼 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" id="code" value="{{ old('code', $company->code ?? $nextCode ?? '') }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror">
                @error('code')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 名稱 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    名稱 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $company->name ?? '') }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 簡稱 -->
            <div>
                <label for="short_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    簡稱 
                    @if(!isset($company))
                        <span class="text-gray-400 text-xs">(選填，未填則使用名稱)</span>
                    @else
                        <span class="text-red-500">*</span>
                    @endif
                </label>
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $company->short_name ?? '') }}" {{ isset($company) ? 'required' : '' }}
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('short_name') border-red-500 @enderror">
                @error('short_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 類型 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    類型 <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="type" value="company" {{ old('type', $company->type ?? 'company') == 'company' ? 'checked' : '' }} required
                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">公司</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="type" value="individual" {{ old('type', $company->type ?? '') == 'individual' ? 'checked' : '' }}
                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">個人</span>
                    </label>
                </div>
                @error('type')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 屬性 -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">屬性</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_client" value="1" {{ old('is_client', $company->is_client ?? false) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">客戶</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_outsource" id="is_outsource_checkbox" value="1" {{ old('is_outsource', $company->is_outsource ?? false) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">外製</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_member" id="is_member_checkbox" value="1" {{ old('is_member', $company->is_member ?? false) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">員工</span>
                    </label>
                </div>
            </div>

            <!-- 統一編號 -->
            <div>
                <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">統一編號</label>
                <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $company->tax_id ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 地址 -->
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">地址</label>
                <input type="text" name="address" id="address" value="{{ old('address', $company->address ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 電話 -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">電話</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 傳真 -->
            <div>
                <label for="fax" class="block text-sm font-medium text-gray-700 dark:text-gray-300">傳真</label>
                <input type="text" name="fax" id="fax" value="{{ old('fax', $company->fax ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $company->email ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 備註 -->
            <div class="md:col-span-2">
                <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備註</label>
                <textarea name="note" id="note" rows="3"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('note', $company->note ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<!-- 個人資訊區塊 (只在選擇員工時顯示) -->
<div id="personal_info_section" class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700" style="display: {{ old('is_member', $company->is_member ?? false) ? 'block' : 'none' }};">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">個人資訊</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 是否在職 -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    是否在職 <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $company->is_active ?? true) == '1' ? 'checked' : '' }} required
                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">是</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="is_active" value="0" {{ old('is_active', $company->is_active ?? true) == '0' ? 'checked' : '' }}
                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">否</span>
                    </label>
                </div>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 到職日 -->
            <div>
                <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">到職日</label>
                <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', isset($company->hire_date) ? $company->hire_date->format('Y-m-d') : '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 離職日 -->
            <div>
                <label for="leave_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">離職日</label>
                <input type="date" name="leave_date" id="leave_date" value="{{ old('leave_date', isset($company->leave_date) ? $company->leave_date->format('Y-m-d') : '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 身分證字號 -->
            <div>
                <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">身分證字號</label>
                <input type="text" name="id_number" id="id_number" value="{{ old('id_number', $company->id_number ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 出生年月日 -->
            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">出生年月日</label>
                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', isset($company->birth_date) ? $company->birth_date->format('Y-m-d') : '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 手機 -->
            <div>
                <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">手機</label>
                <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $company->mobile ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 緊急聯絡人姓名 -->
            <div>
                <label for="emergency_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300">緊急聯絡人姓名</label>
                <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact', $company->emergency_contact ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 緊急聯絡電話 -->
            <div>
                <label for="emergency_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">緊急聯絡電話</label>
                <input type="text" name="emergency_phone" id="emergency_phone" value="{{ old('emergency_phone', $company->emergency_phone ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        
        @if(!isset($company))
        <!-- 建立使用者帳號選項 (僅新增員工時顯示) -->
        <div id="create_user_section" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700" style="display: none;">
            <div class="mb-4">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="create_user_account" id="create_user_account" value="1" {{ old('create_user_account') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">同時建立使用者帳號</span>
                </label>
                <p class="mt-1 ml-6 text-xs text-gray-500 dark:text-gray-400">勾選後將自動為此員工建立系統登入帳號</p>
            </div>
            
            <div id="user_account_fields" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-3">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            使用者帳號資訊
                        </p>
                    </div>
                    
                    <div>
                        <label for="user_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            登入帳號 (Email) <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="user_email" id="user_email" value="{{ old('user_email') }}"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">將用於系統登入</p>
                    </div>
                    
                    <div>
                        <label for="user_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            預設密碼 <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="user_password" id="user_password" value="{{ old('user_password') }}"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">至少 6 個字元</p>
                    </div>
                    
                    <div>
                        <label for="user_role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            角色權限 <span class="text-red-500">*</span>
                        </label>
                        <select name="user_role" id="user_role"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">請選擇</option>
                            <option value="employee" {{ old('user_role') == 'employee' ? 'selected' : '' }}>員工</option>
                            <option value="accountant" {{ old('user_role') == 'accountant' ? 'selected' : '' }}>會計</option>
                            <option value="manager" {{ old('user_role') == 'manager' ? 'selected' : '' }}>主管</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="flex items-center mt-6">
                            <input type="checkbox" name="user_is_active" value="1" {{ old('user_is_active', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">立即啟用帳號</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- 銀行資訊區塊 (廠商和員工顯示) -->
<div id="bank_info_section" style="display: {{ (old('is_outsource', $company->is_outsource ?? false) || old('is_member', $company->is_member ?? false)) ? 'block' : 'none' }};">
    <div class="mt-6">
        <button type="button" id="add_bank_account" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            新增銀行帳號
        </button>
    </div>
    
    <div id="bank_accounts_container">
        <div class="bank-account-wrapper bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mt-6">
            <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">銀行資訊</h3>
            </div>
            
            <div id="bank_accounts_list">
                @php
                    $bankAccounts = (isset($company) && $company->bankAccounts && $company->bankAccounts->count() > 0) 
                        ? $company->bankAccounts 
                        : collect([null]);
                @endphp
                
                @foreach($bankAccounts as $index => $bankAccount)
                <div class="bank-account-item p-6 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">銀行 #{{ $index + 1 }}</label>
                            <input type="text" name="bank_accounts[{{ $index }}][bank_name]" value="{{ $bankAccount->bank_name ?? '' }}"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">分行</label>
                            <input type="text" name="bank_accounts[{{ $index }}][branch_name]" value="{{ $bankAccount->branch_name ?? '' }}"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">帳號</label>
                            <input type="text" name="bank_accounts[{{ $index }}][account_number]" value="{{ $bankAccount->account_number ?? '' }}"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:col-span-3 flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="bank_accounts[{{ $index }}][is_default]" value="1" {{ ($bankAccount->is_default ?? ($index === 0)) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">設為預設帳號</span>
                            </label>
                            <button type="button" class="remove-bank-account text-red-600 hover:text-red-800 text-sm font-medium">移除此帳號</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 監聽員工勾選框 - 控制個人資訊區塊
    const memberCheckbox = document.getElementById('is_member_checkbox');
    const personalInfoSection = document.getElementById('personal_info_section');
    const createUserSection = document.getElementById('create_user_section');
    
    if (memberCheckbox) {
        memberCheckbox.addEventListener('change', function() {
            personalInfoSection.style.display = this.checked ? 'block' : 'none';
            // 只有勾選員工時才顯示建立使用者帳號選項
            if (createUserSection) {
                createUserSection.style.display = this.checked ? 'block' : 'none';
                // 如果取消勾選員工，同時取消勾選建立使用者帳號
                if (!this.checked) {
                    const createUserCheckbox = document.getElementById('create_user_account');
                    if (createUserCheckbox) {
                        createUserCheckbox.checked = false;
                        createUserCheckbox.dispatchEvent(new Event('change'));
                    }
                }
            }
            updateBankInfoVisibility();
        });
    }

    // 監聽建立使用者帳號勾選框
    const createUserCheckbox = document.getElementById('create_user_account');
    const userAccountFields = document.getElementById('user_account_fields');
    
    if (createUserCheckbox && userAccountFields) {
        createUserCheckbox.addEventListener('change', function() {
            userAccountFields.style.display = this.checked ? 'block' : 'none';
            // 根據是否勾選來設定必填欄位
            const userEmailInput = document.getElementById('user_email');
            const userPasswordInput = document.getElementById('user_password');
            const userRoleSelect = document.getElementById('user_role');
            
            if (this.checked) {
                userEmailInput?.setAttribute('required', 'required');
                userPasswordInput?.setAttribute('required', 'required');
                userRoleSelect?.setAttribute('required', 'required');
            } else {
                userEmailInput?.removeAttribute('required');
                userPasswordInput?.removeAttribute('required');
                userRoleSelect?.removeAttribute('required');
            }
        });
        
        // 頁面載入時如果已勾選，顯示欄位
        if (createUserCheckbox.checked) {
            createUserCheckbox.dispatchEvent(new Event('change'));
        }
    }

    // 監聽廠商勾選框 - 控制銀行資訊區塊
    const outsourceCheckbox = document.getElementById('is_outsource_checkbox');
    const bankInfoSection = document.getElementById('bank_info_section');
    
    if (outsourceCheckbox) {
        outsourceCheckbox.addEventListener('change', function() {
            updateBankInfoVisibility();
        });
    }

    // 更新銀行資訊區塊顯示狀態
    function updateBankInfoVisibility() {
        const showBankInfo = memberCheckbox.checked || outsourceCheckbox.checked;
        bankInfoSection.style.display = showBankInfo ? 'block' : 'none';
    }

    // 新增銀行帳號
    let bankAccountIndex = {{ isset($company) && $company->bankAccounts ? $company->bankAccounts->count() : 1 }};
    document.getElementById('add_bank_account')?.addEventListener('click', function() {
        const container = document.getElementById('bank_accounts_list');
        const accountNumber = bankAccountIndex + 1;
        const newAccount = `
            <div class="bank-account-item p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">銀行 #${accountNumber}</label>
                        <input type="text" name="bank_accounts[${bankAccountIndex}][bank_name]"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">分行</label>
                        <input type="text" name="bank_accounts[${bankAccountIndex}][branch_name]"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">帳號</label>
                        <input type="text" name="bank_accounts[${bankAccountIndex}][account_number]"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-3 flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="bank_accounts[${bankAccountIndex}][is_default]" value="1"
                                class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">設為預設帳號</span>
                        </label>
                        <button type="button" class="remove-bank-account text-red-600 hover:text-red-800 text-sm font-medium">移除此帳號</button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newAccount);
        bankAccountIndex++;
    });

    // 移除銀行帳號
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-bank-account')) {
            const item = e.target.closest('.bank-account-item');
            const list = document.getElementById('bank_accounts_list');
            // 至少保留一個銀行帳號欄位
            if (list.querySelectorAll('.bank-account-item').length > 1) {
                item.remove();
                // 更新編號
                updateBankAccountNumbers();
            } else {
                alert('至少需要保留一個銀行帳號欄位');
            }
        }
    });

    // 更新銀行帳號編號
    function updateBankAccountNumbers() {
        const items = document.querySelectorAll('#bank_accounts_list .bank-account-item');
        items.forEach((item, index) => {
            const label = item.querySelector('label');
            if (label && label.textContent.includes('銀行 #')) {
                label.textContent = `銀行 #${index + 1}`;
            }
        });
    }
});
</script>


<div class="mt-6 flex justify-end space-x-3">
    <a href="{{ route('tenant.companies.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 font-bold py-2 px-4 rounded">
        取消
    </a>
    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        {{ isset($company) ? '更新' : '新增' }}
    </button>
</div>