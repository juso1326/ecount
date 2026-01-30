@extends('layouts.tenant')

@section('title', '公司設定')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">公司設定</h1>
        <p class="mt-2 text-gray-600">管理公司基本資料、聯絡方式與銀行資訊</p>
    </div>

    <form action="{{ route('tenant.settings.company.update', $company) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm rounded-lg">
        @csrf
        @method('PUT')

        <!-- 基本資訊 -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">基本資訊</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">公司代碼 *</label>
                    <input type="text" name="code" value="{{ old('code', $company->code) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">公司名稱 *</label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">簡稱</label>
                    <input type="text" name="short_name" value="{{ old('short_name', $company->short_name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">類型 *</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="company" {{ old('type', $company->type) === 'company' ? 'selected' : '' }}>公司</option>
                        <option value="individual" {{ old('type', $company->type) === 'individual' ? 'selected' : '' }}>個人</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_outsource" value="1" {{ old('is_outsource', $company->is_outsource) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">外製</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- 稅務資訊 -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">稅務資訊</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">統一編號</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">發票抬頭</label>
                    <input type="text" name="invoice_title" value="{{ old('invoice_title', $company->invoice_title) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">發票類型</label>
                    <select name="invoice_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="duplicate" {{ old('invoice_type', $company->invoice_type) === 'duplicate' ? 'selected' : '' }}>二聯式</option>
                        <option value="triplicate" {{ old('invoice_type', $company->invoice_type) === 'triplicate' ? 'selected' : '' }}>三聯式</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center pt-8">
                        <input type="checkbox" name="is_tax_entity" value="1" {{ old('is_tax_entity', $company->is_tax_entity ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">課稅主體</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- 聯絡資訊 -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">聯絡資訊</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">負責人</label>
                    <input type="text" name="representative" value="{{ old('representative', $company->representative) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">聯絡人</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $company->contact_person) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">電話</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">手機</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $company->mobile) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">傳真</label>
                    <input type="text" name="fax" value="{{ old('fax', $company->fax) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $company->email) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">地址</label>
                    <input type="text" name="address" value="{{ old('address', $company->address) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Logo 與品牌 -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Logo 與品牌</h2>
            
            @if($company->logo_path)
            <div class="mb-4">
                <img src="{{ Storage::url($company->logo_path) }}" alt="Company Logo" class="h-20 w-auto mb-2">
                <form action="{{ route('tenant.settings.company.logo.delete', $company) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">刪除 Logo</button>
                </form>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">上傳 Logo</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">支援 JPG, PNG, GIF，最大 2MB</p>
                    @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">品牌色碼</label>
                    <input type="color" name="brand_color" value="{{ old('brand_color', $company->brand_color ?? '#3B82F6') }}"
                           class="w-full h-10 border border-gray-300 rounded-lg">
                    <p class="mt-1 text-xs text-gray-500">選擇品牌主色</p>
                </div>
            </div>
        </div>

        <!-- 營業與銀行資訊 -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">營業與銀行資訊</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">營業時間</label>
                    <input type="text" name="business_hours" value="{{ old('business_hours', $company->business_hours) }}"
                           placeholder="例如：週一至週五 09:00-18:00"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">產業別</label>
                    <input type="text" name="industry" value="{{ old('industry', $company->industry) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">資本額</label>
                    <input type="text" name="capital" value="{{ old('capital', $company->capital) }}"
                           placeholder="例如：1,000,000"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">銀行名稱</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $company->bank_name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">分行名稱</label>
                    <input type="text" name="bank_branch" value="{{ old('bank_branch', $company->bank_branch) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">銀行帳號</label>
                    <input type="text" name="bank_account" value="{{ old('bank_account', $company->bank_account) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">戶名</label>
                    <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $company->bank_account_name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- 線上資訊 -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">線上資訊</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">網站</label>
                    <input type="url" name="website" value="{{ old('website', $company->website) }}"
                           placeholder="https://example.com"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Facebook</label>
                    <input type="url" name="facebook" value="{{ old('facebook', $company->facebook) }}"
                           placeholder="https://facebook.com/..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">LINE ID</label>
                    <input type="text" name="line_id" value="{{ old('line_id', $company->line_id) }}"
                           placeholder="@lineid"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                    <input type="text" name="instagram" value="{{ old('instagram', $company->instagram) }}"
                           placeholder="@username"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- 備註 -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">備註</h2>
            
            <textarea name="note" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('note', $company->note) }}</textarea>
        </div>

        <!-- 動作按鈕 -->
        <div class="p-6 flex justify-end gap-3 bg-gray-50">
            <a href="{{ route('tenant.dashboard') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                取消
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                儲存設定
            </button>
        </div>
    </form>
</div>
@endsection
