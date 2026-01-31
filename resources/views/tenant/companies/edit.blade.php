@extends('layouts.tenant')

@section('title', '編輯客戶/廠商')

@section('content')
<!-- Breadcrumb -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black dark:text-white">
        編輯客戶/廠商
    </h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li>
                <a class="font-medium" href="{{ route('tenant.dashboard') }}">首頁 /</a>
            </li>
            <li>
                <a class="font-medium" href="{{ route('tenant.companies.index') }}">客戶/廠商管理 /</a>
            </li>
            <li class="font-medium text-primary">編輯</li>
        </ol>
    </nav>
</div>

<div class="mx-auto max-w-full">
    <div class="grid grid-cols-1 gap-9">
        <div class="flex flex-col gap-9">
            <!-- 表單 -->
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <form method="POST" action="{{ route('tenant.companies.update', $company) }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- 基本資訊 -->
                    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                        <h3 class="font-medium text-black dark:text-white">
                            基本資訊
                        </h3>
                    </div>
                    <div class="p-6.5">
                        <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                            <div class="w-full xl:w-1/2">
                                <label class="mb-2.5 block text-black dark:text-white">
                                    名稱 <span class="text-meta-1">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required
                                    placeholder="請輸入公司/個人名稱"
                                    class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                                @error('name')
                                    <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="w-full xl:w-1/2">
                                <label class="mb-2.5 block text-black dark:text-white">
                                    簡稱 <span class="text-meta-1">*</span>
                                </label>
                                <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $company->short_name) }}" required
                                    placeholder="請輸入簡稱"
                                    class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                                @error('short_name')
                                    <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                            <div class="w-full xl:w-1/2">
                                <label class="mb-2.5 block text-black dark:text-white">
                                    類型 <span class="text-meta-1">*</span>
                                </label>
                                <div class="flex gap-5 pt-2">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="type" value="company" {{ old('type', $company->type) == 'company' ? 'checked' : '' }} required
                                            class="mr-2 h-4 w-4 border-stroke text-primary focus:ring-primary">
                                        <span class="text-black dark:text-white">公司</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="type" value="individual" {{ old('type', $company->type) == 'individual' ? 'checked' : '' }}
                                            class="mr-2 h-4 w-4 border-stroke text-primary focus:ring-primary">
                                        <span class="text-black dark:text-white">個人</span>
                                    </label>
                                </div>
                                @error('type')
                                    <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="w-full xl:w-1/2">
                                <label class="mb-2.5 block text-black dark:text-white">
                                    屬性
                                </label>
                                <div class="flex gap-5 pt-2">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_client" value="1" {{ old('is_client', $company->is_client) ? 'checked' : '' }}
                                            class="mr-2 h-5 w-5 rounded border-stroke text-primary focus:ring-primary">
                                        <span class="text-black dark:text-white">客戶</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_outsource" value="1" {{ old('is_outsource', $company->is_outsource) ? 'checked' : '' }}
                                            class="mr-2 h-5 w-5 rounded border-stroke text-primary focus:ring-primary">
                                        <span class="text-black dark:text-white">外製</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4.5">
                            <label class="mb-2.5 block text-black dark:text-white">
                                統一編號
                            </label>
                            <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $company->tax_id) }}"
                                placeholder="請輸入統一編號"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                            @error('tax_id')
                                <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 聯絡資訊 -->
                    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                        <h3 class="font-medium text-black dark:text-white">
                            聯絡資訊
                        </h3>
                    </div>
                    <div class="p-6.5">
                        <div class="mb-4.5">
                            <label class="mb-2.5 block text-black dark:text-white">
                                地址
                            </label>
                            <input type="text" name="address" id="address" value="{{ old('address', $company->address) }}"
                                placeholder="請輸入地址"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>

                        <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                            <div class="w-full xl:w-1/2">
                                <label class="mb-2.5 block text-black dark:text-white">
                                    電話
                                </label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}"
                                    placeholder="請輸入電話"
                                    class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                                @error('phone')
                                    <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="w-full xl:w-1/2">
                                <label class="mb-2.5 block text-black dark:text-white">
                                    傳真
                                </label>
                                <input type="text" name="fax" id="fax" value="{{ old('fax', $company->fax) }}"
                                    placeholder="請輸入傳真"
                                    class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="mb-2.5 block text-black dark:text-white">
                                Email
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}"
                                placeholder="請輸入 Email"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                            @error('email')
                                <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 按鈕 -->
                        <div class="flex justify-end gap-4.5">
                            <a href="{{ route('tenant.companies.index') }}" 
                               class="flex justify-center rounded border border-stroke py-2 px-6 font-medium text-black hover:shadow-1 dark:border-strokedark dark:text-white">
                                取消
                            </a>
                            <button type="submit" 
                                    class="flex justify-center rounded bg-primary py-2 px-6 font-medium text-gray hover:bg-opacity-90">
                                更新
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
