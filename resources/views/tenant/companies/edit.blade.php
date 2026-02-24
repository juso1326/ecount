@extends('layouts.tenant')

@section('title', '編輯客戶/廠商')

@section('content')
<div class="mb-3">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">編輯客戶/廠商</h1>
</div>

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <form method="POST" action="{{ route('tenant.companies.update', $company) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <!-- 公司代碼 (唯讀，建立後不可修改) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">公司代碼</label>
                <input type="text" value="{{ $company->code }}" disabled
                    class="mt-1 block w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-400 rounded-md shadow-sm py-2 px-3 bg-gray-50 text-gray-500 cursor-not-allowed">
                <input type="hidden" name="code" value="{{ old('code', $company->code) }}">
                <p class="mt-1 text-xs text-gray-400">公司代碼建立後不可修改</p>
            </div>

            <!-- 類型 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    類型 <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="type" value="company" {{ old('type', $company->type) == 'company' ? 'checked' : '' }} required
                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">公司</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="type" value="individual" {{ old('type', $company->type) == 'individual' ? 'checked' : '' }}
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
                        <input type="checkbox" name="is_client" value="1" {{ old('is_client', $company->is_client) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">客戶</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_outsource" value="1" {{ old('is_outsource', $company->is_outsource) ? 'checked' : '' }}
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
                <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required
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
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $company->short_name) }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('short_name') border-red-500 @enderror">
                @error('short_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 統一編號 -->
            <div>
                <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">統一編號</label>
                <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $company->tax_id) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 電話 -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">電話</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 地址 -->
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">地址</label>
                <input type="text" name="address" id="address" value="{{ old('address', $company->address) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 傳真 -->
            <div>
                <label for="fax" class="block text-sm font-medium text-gray-700 dark:text-gray-300">傳真</label>
                <input type="text" name="fax" id="fax" value="{{ old('fax', $company->fax) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 聯絡人 -->
            <div>
                <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300">聯絡人</label>
                <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $company->contact_person) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 代表人 -->
            <div>
                <label for="representative" class="block text-sm font-medium text-gray-700 dark:text-gray-300">代表人</label>
                <input type="text" name="representative" id="representative" value="{{ old('representative', $company->representative) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 手機 -->
            <div>
                <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">手機</label>
                <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $company->mobile) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 網站 -->
            <div>
                <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">網站</label>
                <input type="text" name="website" id="website" value="{{ old('website', $company->website) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://...">
            </div>
        </div>

        <!-- 發票資訊 -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">發票資訊</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label for="invoice_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">發票抬頭</label>
                    <input type="text" name="invoice_title" id="invoice_title" value="{{ old('invoice_title', $company->invoice_title) }}"
                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">開立發票</label>
                    <div class="flex items-center mt-2">
                        <input type="checkbox" name="is_tax_entity" id="is_tax_entity" value="1" {{ old('is_tax_entity', $company->is_tax_entity) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <label for="is_tax_entity" class="ml-2 text-sm text-gray-600 dark:text-gray-400">需開立發票</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- 銀行資訊 -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">銀行資訊</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">銀行名稱</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $company->bank_name) }}"
                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="例：台灣銀行">
                </div>
                <div>
                    <label for="bank_branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">分行</label>
                    <input type="text" name="bank_branch" id="bank_branch" value="{{ old('bank_branch', $company->bank_branch) }}"
                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="bank_account" class="block text-sm font-medium text-gray-700 dark:text-gray-300">帳號</label>
                    <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account', $company->bank_account) }}"
                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="bank_account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">戶名</label>
                    <input type="text" name="bank_account_name" id="bank_account_name" value="{{ old('bank_account_name', $company->bank_account_name) }}"
                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- 備註 -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備註</label>
            <textarea name="note" id="note" rows="3"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('note', $company->note) }}</textarea>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('tenant.companies.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 font-bold py-2 px-4 rounded">
                取消
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                更新
            </button>
        </div>
    </form>
</div>
@endsection
