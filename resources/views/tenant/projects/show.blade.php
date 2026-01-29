@extends('layouts.tenant')

@section('title', '專案詳情')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">專案詳情</h1>
    <div class="space-x-2">
        <a href="{{ route('tenant.projects.edit', $project) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            編輯
        </a>
        <a href="{{ route('tenant.projects.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
            返回列表
        </a>
    </div>
</div>

<!-- 基本資訊 -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">基本資訊</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-500">專案代碼</label>
            <p class="mt-1 text-lg text-gray-900">{{ $project->code }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">專案名稱</label>
            <p class="mt-1 text-lg text-gray-900">{{ $project->name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">所屬公司</label>
            <p class="mt-1 text-lg text-gray-900">
                <a href="{{ route('tenant.companies.show', $project->company) }}" class="text-blue-600 hover:text-blue-900">
                    {{ $project->company->name }}
                </a>
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">所屬部門</label>
            <p class="mt-1 text-lg text-gray-900">
                @if($project->department)
                    <a href="{{ route('tenant.departments.show', $project->department) }}" class="text-blue-600 hover:text-blue-900">
                        {{ $project->department->name }}
                    </a>
                @else
                    -
                @endif
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">專案經理</label>
            <p class="mt-1 text-lg text-gray-900">{{ $project->manager?->name ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">專案狀態</label>
            <p class="mt-1">
                @php
                    $statusColors = [
                        'planning' => 'bg-gray-100 text-gray-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'on_hold' => 'bg-yellow-100 text-yellow-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                    $statusNames = [
                        'planning' => '規劃中',
                        'in_progress' => '進行中',
                        'on_hold' => '暫停',
                        'completed' => '已完成',
                        'cancelled' => '已取消',
                    ];
                @endphp
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusNames[$project->status] ?? $project->status }}
                </span>
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">開始日期</label>
            <p class="mt-1 text-lg text-gray-900">{{ $project->start_date?->format('Y-m-d') ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">結束日期</label>
            <p class="mt-1 text-lg text-gray-900">{{ $project->end_date?->format('Y-m-d') ?? '-' }}</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-500">專案描述</label>
            <p class="mt-1 text-lg text-gray-900">{{ $project->description ?? '-' }}</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-500">備註</label>
            <p class="mt-1 text-lg text-gray-900">{{ $project->note ?? '-' }}</p>
        </div>
    </div>
</div>

<!-- 預算資訊 -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">預算資訊</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-500">預算金額</label>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($project->budget) }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">實際成本</label>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($project->actual_cost) }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">剩餘預算</label>
            <p class="mt-1 text-2xl font-bold {{ $project->remaining_budget >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ number_format($project->remaining_budget) }}
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">預算使用率</label>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($project->budget_usage_percentage, 1) }}%</p>
        </div>
    </div>
    
    <!-- 預算進度條 -->
    <div class="mt-4">
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="h-4 rounded-full {{ $project->budget_usage_percentage > 100 ? 'bg-red-500' : ($project->budget_usage_percentage > 80 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                style="width: {{ min($project->budget_usage_percentage, 100) }}%"></div>
        </div>
    </div>
</div>

<!-- 專案成員 -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">專案成員</h2>
    
    @if($project->members->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($project->members as $member)
                <div class="border rounded p-4">
                    <p class="font-medium">{{ $member->name }}</p>
                    <p class="text-sm text-gray-500">{{ $member->email }}</p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">尚無專案成員</p>
    @endif
</div>

<!-- 應收帳款 -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">應收帳款</h2>
    
    @if($project->receivables->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">應收日期</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">金額</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">已收金額</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">付款進度</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">狀態</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($project->receivables as $receivable)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $receivable->due_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($receivable->amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($receivable->paid_amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($receivable->payment_progress, 1) }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded text-xs 
                                @if($receivable->status === 'paid') bg-green-100 text-green-800
                                @elseif($receivable->status === 'partial') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $receivable->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">尚無應收帳款</p>
    @endif
</div>

<!-- 應付帳款 -->
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">應付帳款</h2>
    
    @if($project->payables->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">應付日期</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">金額</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">已付金額</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">付款進度</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">狀態</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($project->payables as $payable)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $payable->due_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($payable->amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($payable->paid_amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($payable->payment_progress, 1) }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded text-xs 
                                @if($payable->status === 'paid') bg-green-100 text-green-800
                                @elseif($payable->status === 'partial') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $payable->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">尚無應付帳款</p>
    @endif
</div>
@endsection
