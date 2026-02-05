@extends('layouts.tenant')

@section('title', '公司設定')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">公司設定</h1>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <form method="POST" action="{{ route('tenant.settings.company.update') }}">
        @csrf
        
        <div class="space-y-6">
            @foreach($settings as $setting)
            <div>
                <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ $setting->label }}
                </label>
                
                @if($setting->type === 'boolean')
                    <label class="flex items-center">
                        <input type="checkbox" name="{{ $setting->key }}" value="true" 
                            {{ $setting->getValue() ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">啟用</span>
                    </label>
                @elseif($setting->type === 'number')
                    <input type="number" name="{{ $setting->key }}" id="{{ $setting->key }}" 
                        value="{{ $setting->getValue() }}"
                        class="mt-1 block w-full md:w-1/3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @else
                    <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}" 
                        value="{{ $setting->getValue() }}"
                        class="mt-1 block w-full md:w-1/2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @endif
                
                @if($setting->description)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('tenant.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 font-bold py-2 px-4 rounded">
                取消
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                儲存
            </button>
        </div>
    </form>
</div>

<!-- 預覽下一個代碼 -->
@php
    $prefix = \App\Models\TenantSetting::get('company_code_prefix', 'C');
    $length = \App\Models\TenantSetting::get('company_code_length', 4);
    $lastCompany = \App\Models\Company::where('code', 'like', $prefix . '%')->orderBy('code', 'desc')->first();
    $nextNumber = $lastCompany ? ((int) str_replace($prefix, '', $lastCompany->code)) + 1 : 1;
    $nextCode = $prefix . str_pad((string)$nextNumber, $length, '0', STR_PAD_LEFT);
@endphp

<div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">預覽</h3>
    <p class="text-sm text-blue-700 dark:text-blue-400">
        根據目前設定，下一個公司代碼將會是：<strong class="font-mono">{{ $nextCode }}</strong>
    </p>
</div>
@endsection
