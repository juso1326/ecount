@extends('layouts.tenant')

@section('title', '新增客戶/廠商')

@section('content')
<div class="mb-3">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">新增客戶/廠商</h1>
</div>

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <form method="POST" action="{{ route('tenant.companies.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <!-- 公司代碼 -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    公司代碼 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" id="code" value="{{ old('code', $nextCode ?? '') }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror">
                @error('code')
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
                        <input type="radio" name="type" value="company" {{ old('type', 'company') == 'company' ? 'checked' : '' }} required
                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">公司</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="type" value="individual" {{ old('type') == 'individual' ? 'checked' : '' }}
                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">個人</span>
                    </label>
                </div>
                @error('type')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 屬性 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">屬性</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_client" value="1" {{ old('is_client') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">客戶</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_outsource" value="1" {{ old('is_outsource') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">外製</span>
                    </label>
                </div>
            </div>

            <!-- 名稱 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    名稱 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 簡稱 -->
            <div>
                <label for="short_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    簡稱 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name') }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('short_name') border-red-500 @enderror">
                @error('short_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 電話 -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">電話</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 地址 -->
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">地址</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <!-- 發票資訊 -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">發票資訊</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                <div>
                    <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">統一編號</label>
                    <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}"
                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('tax_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="invoice_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">發票抬頭</label>
                    <input type="text" name="invoice_title" id="invoice_title" value="{{ old('invoice_title') }}"
                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-center pb-2">
                    <input type="checkbox" name="is_tax_entity" id="is_tax_entity" value="1" {{ old('is_tax_entity') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="is_tax_entity" class="ml-2 text-sm text-gray-600 dark:text-gray-400">需開立發票</label>
                </div>
            </div>
        </div>

        <!-- 聯絡人 -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">聯絡人</h3>
                <button type="button" id="addContactBtn"
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    新增聯絡人
                </button>
            </div>
            <div id="contactList" class="space-y-3">
                @php $contacts = old('contacts', [[]]); @endphp
                @foreach($contacts as $ci => $contact)
                <div class="contact-row relative {{ $ci > 0 ? 'pt-3' : '' }}">
                    <button type="button" onclick="removeContactRow(this)"
                        class="absolute top-2 right-2 text-red-400 hover:text-red-600 text-xs">✕</button>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">姓名</label>
                            <input type="text" name="contacts[{{ $ci }}][name]"
                                value="{{ $contact['name'] ?? '' }}"
                                class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="姓名">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">公司電話</label>
                            <input type="text" name="contacts[{{ $ci }}][phone]"
                                value="{{ $contact['phone'] ?? '' }}"
                                class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">手機</label>
                            <input type="text" name="contacts[{{ $ci }}][mobile]"
                                value="{{ $contact['mobile'] ?? '' }}"
                                class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Email</label>
                            <input type="email" name="contacts[{{ $ci }}][email]"
                                value="{{ $contact['email'] ?? '' }}"
                                class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <script>
        let contactIndex = {{ count($contacts) }};
        document.getElementById('addContactBtn').addEventListener('click', function() {
            const tpl = `<div class="contact-row pt-3 relative">
                <button type="button" onclick="removeContactRow(this)" class="absolute top-2 right-2 text-red-400 hover:text-red-600 text-xs">✕</button>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">姓名</label>
                    <input type="text" name="contacts[${contactIndex}][name]" class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="姓名"></div>
                    <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">公司電話</label>
                    <input type="text" name="contacts[${contactIndex}][phone]" class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">手機</label>
                    <input type="text" name="contacts[${contactIndex}][mobile]" class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Email</label>
                    <input type="email" name="contacts[${contactIndex}][email]" class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>
                </div>
            </div>`;
            document.getElementById('contactList').insertAdjacentHTML('beforeend', tpl);
            contactIndex++;
        });
        function removeContactRow(btn) {
            const rows = document.querySelectorAll('#contactList .contact-row');
            if (rows.length > 1) btn.closest('.contact-row').remove();
        }
        </script>

        <!-- 銀行資訊 -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">銀行資訊</h3>
                <button type="button" id="addBankBtn"
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    新增銀行帳戶
                </button>
            </div>
            <div id="bankList" class="space-y-3">
                @php $bankAccounts = old('bank_accounts', [[]]); @endphp
                @foreach($bankAccounts as $i => $bank)
                <div class="bank-row {{ $i > 0 ? 'pt-3' : '' }} relative">
                    <button type="button" onclick="removeBankRow(this)"
                        class="absolute top-2 right-2 text-red-400 hover:text-red-600 text-xs">✕</button>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">銀行名稱</label>
                            <input type="text" name="bank_accounts[{{ $i }}][bank_name]"
                                value="{{ $bank['bank_name'] ?? '' }}"
                                class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="例：台灣銀行">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">分行</label>
                            <input type="text" name="bank_accounts[{{ $i }}][bank_branch]"
                                value="{{ $bank['bank_branch'] ?? '' }}"
                                class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">帳號</label>
                            <input type="text" name="bank_accounts[{{ $i }}][bank_account]"
                                value="{{ $bank['bank_account'] ?? '' }}"
                                class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">戶名</label>
                            <input type="text" name="bank_accounts[{{ $i }}][bank_account_name]"
                                value="{{ $bank['bank_account_name'] ?? '' }}"
                                class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">備註</label>
                        <input type="text" name="bank_accounts[{{ $i }}][note]"
                            value="{{ $bank['note'] ?? '' }}"
                            class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="備註（選填）">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <script>
        let bankIndex = {{ count($bankAccounts) }};
        document.getElementById('addBankBtn').addEventListener('click', function() {
            const tpl = `<div class="bank-row pt-3 relative">
                <button type="button" onclick="removeBankRow(this)" class="absolute top-2 right-2 text-red-400 hover:text-red-600 text-xs">✕</button>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">銀行名稱</label>
                    <input type="text" name="bank_accounts[${bankIndex}][bank_name]" class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="例：台灣銀行"></div>
                    <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">分行</label>
                    <input type="text" name="bank_accounts[${bankIndex}][bank_branch]" class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">帳號</label>
                    <input type="text" name="bank_accounts[${bankIndex}][bank_account]" class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">戶名</label>
                    <input type="text" name="bank_accounts[${bankIndex}][bank_account_name]" class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>
                </div>
                <div class="mt-2"><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">備註</label>
                <input type="text" name="bank_accounts[${bankIndex}][note]" class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="備註（選填）"></div>
            </div>`;
            document.getElementById('bankList').insertAdjacentHTML('beforeend', tpl);
            bankIndex++;
        });
        function removeBankRow(btn) {
            const rows = document.querySelectorAll('#bankList .bank-row');
            if (rows.length > 1) btn.closest('.bank-row').remove();
        }
        </script>

        <!-- 備註 -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備註</label>
            <textarea name="note" id="note" rows="3"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('note') }}</textarea>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('tenant.companies.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 font-bold py-2 px-4 rounded">
                取消
            </a>
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded">
                新增
            </button>
        </div>
    </form>
</div>
@endsection
