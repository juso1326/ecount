@extends('layouts.tenant')

@section('title', '編輯客戶/廠商')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">編輯客戶/廠商</h1>
</div>

<!-- <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6"> -->
    <form method="POST" action="{{ route('tenant.companies.update', $company) }}">
        @csrf
        @method('PUT')
        
        @include('tenant.companies._form')
    </form>
<!-- </div> -->

<!-- 屬性變更歷史 -->
@if(isset($attributeHistories) && $attributeHistories->count() > 0)
<div class="mt-6 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">屬性變更歷史</h2>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">變更時間</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">屬性</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">變更前</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">變更後</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">變更人</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($attributeHistories as $history)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                        {{ $history->changed_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                        {{ $history->attribute_display_name }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $history->old_value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $history->getValueDisplay($history->old_value) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $history->new_value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $history->getValueDisplay($history->new_value) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                        {{ $history->changedBy ? $history->changedBy->name : '系統' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
