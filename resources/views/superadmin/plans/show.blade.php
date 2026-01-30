@extends('layouts.superadmin')

@section('title', $plan->name)
@section('page-title', '方案詳情')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.plans.index') }}" class="text-primary hover:underline">&larr; 返回方案列表</a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $plan->slug }}</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('superadmin.plans.toggle-active', $plan) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 {{ $plan->is_active ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} rounded-lg">
                    {{ $plan->is_active ? '停用' : '啟用' }}
                </button>
            </form>
            <a href="{{ route('superadmin.plans.edit', $plan) }}" class="px-4 py-2 bg-primary text-white rounded-lg">編輯</a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <h3 class="font-semibold mb-2">價格資訊</h3>
            <p>月費: ${{ number_format($plan->price, 2) }}</p>
            @if($plan->annual_price)
            <p>年費: ${{ number_format($plan->annual_price, 2) }}</p>
            @endif
        </div>
        <div>
            <h3 class="font-semibold mb-2">使用限制</h3>
            @if($plan->max_users)<p>最大使用者: {{ $plan->max_users }}</p>@endif
            @if($plan->max_companies)<p>最大公司: {{ $plan->max_companies }}</p>@endif
        </div>
    </div>

    <div class="mt-6">
        <h3 class="font-semibold mb-2">使用此方案的租戶 ({{ $plan->tenants->count() }})</h3>
        @forelse($plan->tenants as $tenant)
        <div class="border-t py-2">
            <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="text-primary hover:underline">
                {{ $tenant->name ?: $tenant->id }}
            </a>
        </div>
        @empty
        <p class="text-gray-500">尚無租戶使用此方案</p>
        @endforelse
    </div>
</div>
@endsection
