@extends('layouts.tenant')

@section('title', '專案詳情')

@section('page-title', '專案詳情')

@section('content')
<!-- 頁面標題與按鈕 -->
<div class="mb-3 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">專案詳情</h1>
    <div class="flex gap-3">
        <a href="{{ route('tenant.projects.show', $project) }}" 
           class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
            編輯
        </a>
        <a href="{{ route('tenant.projects.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
            返回列表
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-3 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-3 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded">
        {{ session('error') }}
    </div>
@endif

<!-- 內容區域 - 左右佈局 -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-2">
    <!-- 左側：專案資訊 (2/3寬度) -->
    <div class="lg:col-span-2 space-y-2">
        <!-- 基本資訊 -->
        <div id="basic-info" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                基本資訊
            </h2>
            
            <!-- 快速編輯表單 -->
            <form method="POST" action="{{ route('tenant.projects.quick-update', $project) }}" class="mb-2">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm mb-3">
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">狀態 <span class="text-red-500">*</span></label>
                        <select name="status" required
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-1.5 px-2 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @foreach($projectStatuses as $s)
                                <option value="{{ $s['value'] }}" {{ $project->status == $s['value'] ? 'selected' : '' }}>
                                    {{ $s['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">執行日期</label>
                        <input type="date" name="start_date" value="{{ $project->start_date?->format('Y-m-d') }}"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-1.5 px-2 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">結束日期</label>
                        <input type="date" name="end_date" value="{{ $project->end_date?->format('Y-m-d') }}"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-1.5 px-2 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">備註</label>
                        <textarea name="note" rows="3"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-1.5 px-2 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ $project->note }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-1.5 px-4 rounded text-sm">
                        更新
                    </button>
                </div>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm pt-3 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">專案代碼</label>
                    <p class="mt-0.5 text-gray-900 dark:text-white">{{ $project->code }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">專案名稱</label>
                    <p class="mt-0.5 text-gray-900 dark:text-white">{{ $project->name }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">所屬客戶</label>
                    <p class="mt-0.5 text-gray-900 dark:text-white">{{ $project->company?->name ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">專案經理</label>
                    <p class="mt-0.5 text-gray-900 dark:text-white">{{ $project->manager?->name ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">結束日期</label>
                    <p class="mt-0.5 text-gray-900 dark:text-white">{{ format_date($project->end_date) }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">預算金額</label>
                    <p class="mt-0.5 text-gray-900 dark:text-white">NT$ {{ number_format($project->budget, 0) }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">實際成本</label>
                    <p class="mt-0.5 text-gray-900 dark:text-white">NT$ {{ number_format($project->actual_cost, 0) }}</p>
                </div>
            </div>
        </div>

        <!-- 專案描述 -->
        @if($project->description)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                專案描述
            </h2>
            <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $project->description }}</div>
        </div>
        @endif

        <!-- 備註 -->
        @if($project->note)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                備註
            </h2>
            <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $project->note }}</div>
        </div>
        @endif
    </div>

    <!-- 右側：專案標籤與成員 (1/3寬度) -->
    <div class="lg:col-span-1 space-y-2">
        <!-- 專案標籤 -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                專案標籤
            </h2>
            
            <form action="{{ route('tenant.projects.tags.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <select name="tags[]" id="projectTags" multiple
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                        @foreach(\App\Models\Tag::ofType('project')->orderBy('name')->get() as $tag)
                            <option value="{{ $tag->id }}" 
                                    {{ $project->tags->contains($tag->id) ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg text-sm">
                    更新標籤
                </button>
            </form>
        </div>
        
        <!-- 專案成員 -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    專案成員 ({{ $project->members()->count() }})
                </h2>
                <button onclick="openAddMemberModal()"
                        class="text-primary hover:text-primary-dark">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
            
            @php
                $projectMembers = $project->members ?? collect();
            @endphp
            
            @if($projectMembers->count() > 0)
                <div class="space-y-2">
                    @foreach($projectMembers as $member)
                    <div class="border border-gray-200 dark:border-gray-700 rounded p-2">
                        <div class="flex justify-between items-start mb-1">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->email }}</p>
                                @if($member->pivot->role)
                                    <p class="text-xs text-gray-400 dark:text-gray-500.5">{{ $member->pivot->role }}</p>
                                @endif
                            </div>
                            <form action="{{ route('tenant.projects.members.remove', [$project, $member]) }}" 
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4 text-sm">尚無專案成員</p>
            @endif
        </div>
    </div>
</div>

<!-- 應收帳款（全寬表格） -->
@php
    $receivables = $project->receivables;
    $totalReceivable = $receivables->sum('amount');
    $totalReceived = $receivables->sum('received_amount');
    $totalRemaining = $totalReceivable - $totalReceived;
@endphp
<div class="mt-2 bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="flex flex-wrap items-center gap-3 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white shrink-0">應收帳款</h2>
        @if($receivables->count() > 0)
        <div class="flex items-center gap-4 text-sm flex-1">
            <span class="text-gray-500 dark:text-gray-400">應收總額 <span class="text-blue-600 dark:text-blue-400 font-semibold">NT$ {{ number_format($totalReceivable, 0) }}</span></span>
            <span class="text-gray-500 dark:text-gray-400">已收 <span class="text-green-600 dark:text-green-400 font-semibold">NT$ {{ number_format($totalReceived, 0) }}</span></span>
            <span class="text-gray-500 dark:text-gray-400">未收 <span class="text-orange-600 dark:text-orange-400 font-semibold">NT$ {{ number_format($totalRemaining, 0) }}</span></span>
        </div>
        @endif
        <button onclick="document.getElementById('addReceivableModal').classList.remove('hidden')"
                class="text-blue-500 hover:text-blue-700 text-sm font-medium shrink-0">+ 快速新增</button>
    </div>
    @if($receivables->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="text-center px-2 py-2 font-medium whitespace-nowrap">編輯</th>
                        <th class="text-left px-3 py-2 font-medium whitespace-nowrap">開立日</th>
                        <th class="text-left px-3 py-2 font-medium">內容</th>
                        <th class="text-left px-3 py-2 font-medium whitespace-nowrap">發票號碼</th>
                        <th class="text-right px-3 py-2 font-medium whitespace-nowrap">未稅</th>
                        <th class="text-right px-3 py-2 font-medium whitespace-nowrap">應收金額</th>
                        <th class="text-left px-3 py-2 font-medium whitespace-nowrap">入帳日</th>
                        <th class="text-right px-3 py-2 font-medium whitespace-nowrap">實收</th>
                        <th class="text-right px-3 py-2 font-medium whitespace-nowrap">扣繳</th>
                        <th class="text-right px-3 py-2 font-medium whitespace-nowrap">合計</th>
                        <th class="text-center px-3 py-2 font-medium whitespace-nowrap">狀態</th>
                        <th class="text-left px-3 py-2 font-medium">備註</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($receivables as $receivable)
                    @php $net = ($receivable->received_amount ?? 0) - ($receivable->withholding_tax ?? 0); @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-2 py-2 text-center whitespace-nowrap">
                            <a href="{{ route('tenant.receivables.edit', $receivable) }}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400">編輯</a>
                        </td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ format_date($receivable->receipt_date) }}</td>
                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ $receivable->content ?: '—' }}</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $receivable->invoice_no ?: '—' }}</td>
                        <td class="px-3 py-2 text-right text-gray-500 dark:text-gray-400 whitespace-nowrap">${{ number_format($receivable->amount_before_tax ?? 0, 0) }}</td>
                        <td class="px-3 py-2 text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap">${{ number_format($receivable->amount, 0) }}</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $receivable->paid_date ? format_date($receivable->paid_date) : '—' }}</td>
                        <td class="px-3 py-2 text-right text-green-600 dark:text-green-400 whitespace-nowrap">${{ number_format($receivable->received_amount ?? 0, 0) }}</td>
                        <td class="px-3 py-2 text-right text-orange-500 dark:text-orange-400 whitespace-nowrap">${{ number_format($receivable->withholding_tax ?? 0, 0) }}</td>
                        <td class="px-3 py-2 text-right font-semibold whitespace-nowrap {{ $net >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">${{ number_format($net, 0) }}</td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            @if($receivable->status === 'paid') <span class="px-1.5 py-0.5 rounded-full text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">已收</span>
                            @elseif($receivable->status === 'partial') <span class="px-1.5 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-800">部分</span>
                            @elseif($receivable->status === 'overdue') <span class="px-1.5 py-0.5 rounded-full text-xs bg-red-100 text-red-800">逾期</span>
                            @else <span class="px-1.5 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">待收</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $receivable->note ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400 text-center py-6 text-sm">尚無應收帳款</p>
    @endif
</div>

<!-- 應付帳款（全寬表格） -->
@php
    $payables = $project->payables;
    $totalPayable = $payables->sum('amount');
    $totalPaid = $payables->sum('paid_amount');
    $totalUnpaid = $totalPayable - $totalPaid;
@endphp
<div class="mt-2 bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="flex flex-wrap items-center gap-3 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white shrink-0">應付帳款</h2>
        @if($payables->count() > 0)
        <div class="flex items-center gap-4 text-sm flex-1">
            <span class="text-gray-500 dark:text-gray-400">應付總額 <span class="text-blue-600 dark:text-blue-400 font-semibold">NT$ {{ number_format($totalPayable, 0) }}</span></span>
            <span class="text-gray-500 dark:text-gray-400">已付 <span class="text-green-600 dark:text-green-400 font-semibold">NT$ {{ number_format($totalPaid, 0) }}</span></span>
            <span class="text-gray-500 dark:text-gray-400">未付 <span class="text-orange-600 dark:text-orange-400 font-semibold">NT$ {{ number_format($totalUnpaid, 0) }}</span></span>
        </div>
        @endif
        <button onclick="document.getElementById('addPayableModal').classList.remove('hidden')"
                class="text-blue-500 hover:text-blue-700 text-sm font-medium shrink-0">+ 快速新增</button>
    </div>
    @if($payables->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="text-center px-2 py-2 font-medium whitespace-nowrap">編輯</th>
                        <th class="text-left px-3 py-2 font-medium whitespace-nowrap">日期</th>
                        <th class="text-left px-3 py-2 font-medium whitespace-nowrap">類型</th>
                        <th class="text-left px-3 py-2 font-medium whitespace-nowrap">對象</th>
                        <th class="text-left px-3 py-2 font-medium">內容</th>
                        <th class="text-right px-3 py-2 font-medium whitespace-nowrap">預算</th>
                        <th class="text-right px-3 py-2 font-medium whitespace-nowrap">比例</th>
                        <th class="text-left px-3 py-2 font-medium whitespace-nowrap">付款日</th>
                        <th class="text-left px-3 py-2 font-medium whitespace-nowrap">發票日</th>
                        <th class="text-left px-3 py-2 font-medium whitespace-nowrap">發票號碼</th>
                        <th class="text-right px-3 py-2 font-medium whitespace-nowrap">扣抵</th>
                        <th class="text-right px-3 py-2 font-medium whitespace-nowrap">實付</th>
                        <th class="text-center px-3 py-2 font-medium whitespace-nowrap">狀態</th>
                        <th class="text-left px-3 py-2 font-medium">備註</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($payables as $payable)
                    @php
                        $ratio = $totalReceivable > 0 ? round($payable->amount / $totalReceivable * 100, 1) : 0;
                        $payeeLabel = match($payable->payee_type) {
                            'member', 'user' => '成員',
                            'vendor', 'company' => '外包',
                            'expense' => '採購',
                            default => '其他',
                        };
                        $payeeName = $payable->payee_type === 'member' || $payable->payee_type === 'user'
                            ? ($payable->payeeUser?->name ?? '-')
                            : ($payable->payeeCompany?->short_name ?? $payable->payeeCompany?->name ?? $payable->company?->short_name ?? $payable->company?->name ?? '-');
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-2 py-2 text-center whitespace-nowrap">
                            <a href="{{ route('tenant.payables.edit', $payable) }}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400">編輯</a>
                        </td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ format_date($payable->payment_date) }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="px-1.5 py-0.5 rounded-full text-xs
                                @if($payable->payee_type === 'member' || $payable->payee_type === 'user') bg-blue-100 text-blue-800
                                @elseif($payable->payee_type === 'vendor' || $payable->payee_type === 'company') bg-purple-100 text-purple-800
                                @elseif($payable->payee_type === 'expense') bg-orange-100 text-orange-800
                                @else bg-gray-100 text-gray-700 @endif">{{ $payeeLabel }}</span>
                        </td>
                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $payeeName }}</td>
                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ $payable->content ?: '—' }}</td>
                        <td class="px-3 py-2 text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap">${{ number_format($payable->amount, 0) }}</td>
                        <td class="px-3 py-2 text-right text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $ratio }}%</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $payable->paid_date ? format_date($payable->paid_date) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $payable->invoice_date ? format_date($payable->invoice_date) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $payable->invoice_no ?: '—' }}</td>
                        <td class="px-3 py-2 text-right text-orange-500 dark:text-orange-400 whitespace-nowrap">${{ number_format($payable->deduction ?? 0, 0) }}</td>
                        <td class="px-3 py-2 text-right text-green-600 dark:text-green-400 whitespace-nowrap">${{ number_format($payable->paid_amount ?? 0, 0) }}</td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            @if($payable->status === 'paid') <span class="px-1.5 py-0.5 rounded-full text-xs bg-green-100 text-green-800">已付</span>
                            @elseif($payable->status === 'partial') <span class="px-1.5 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-800">部分</span>
                            @elseif($payable->status === 'overdue') <span class="px-1.5 py-0.5 rounded-full text-xs bg-red-100 text-red-800">逾期</span>
                            @else <span class="px-1.5 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">未付</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $payable->note ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400 text-center py-6 text-sm">尚無應付帳款</p>
    @endif
</div>

<!-- 系統資訊 -->
<div class="mt-4 bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-3">
    <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 dark:text-gray-400">
        <div>建立時間：{{ format_datetime($project->created_at) }}</div>
        <div>最後更新：{{ format_datetime($project->updated_at) }}</div>
    </div>
</div>

<!-- 新增成員 Modal -->
<div id="addMemberModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">新增專案成員</h3>
            
            <form action="{{ route('tenant.projects.members.add', $project) }}" method="POST">
                @csrf
                
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        選擇成員 <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="memberUserSelect" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="">請選擇</option>
                        @foreach($availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        角色/職務
                    </label>
                    <select name="role" id="memberRoleSelect"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                        <option value=""></option>
                        @foreach($projectRoles as $roleName)
                            <option value="{{ $roleName }}">{{ $roleName }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">可從清單選擇，或直接輸入新職務（自動加入標籤庫）</p>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button"
                            onclick="document.getElementById('addMemberModal').classList.add('hidden')"
                            class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
                        取消
                    </button>
                    <button type="submit"
                            class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg">
                        新增
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 快速新增應收帳款 Modal -->
<div id="addReceivableModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">快速新增應收帳款</h3>
            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                <p>專案：{{ $project->name }}</p>
                <p>客戶：{{ $project->company?->name ?? '-' }} | 統編：{{ $project->company?->tax_id ?? '-' }}</p>
            </div>
            
            <form action="{{ route('tenant.projects.receivables.quick-add', $project) }}" method="POST">
                @csrf
                
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                收款日期 <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="receipt_date" value="{{ date('Y-m-d') }}" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                金額 <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="amount" step="1" min="0" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            內容說明
                        </label>
                        <textarea name="content" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">{{ $project->name }}</textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                發票日
                            </label>
                            <input type="date" name="due_date"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                發票號碼
                            </label>
                            <input type="text" name="invoice_no"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            備註
                        </label>
                        <textarea name="note" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button"
                            onclick="document.getElementById('addReceivableModal').classList.add('hidden')"
                            class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
                        取消
                    </button>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
                        新增
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 快速新增應付帳款 Modal -->
<div id="addPayableModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">快速新增應付帳款</h3>
            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                <p>專案：{{ $project->name }}</p>
                <p>客戶：{{ $project->company?->name ?? '-' }} | 統編：{{ $project->company?->tax_id ?? '-' }}</p>
            </div>
            
            <form action="{{ route('tenant.projects.payables.quick-add', $project) }}" method="POST">
                @csrf
                
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                付款日期 <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                金額 <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="amount" step="1" min="0" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            收款對象
                        </label>
                        <input type="text" name="vendor"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            內容說明
                        </label>
                        <textarea name="content" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">{{ $project->name }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            備註
                        </label>
                        <textarea name="note" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button"
                            onclick="document.getElementById('addPayableModal').classList.add('hidden')"
                            class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
                        取消
                    </button>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
                        新增
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 快速編輯應收帳款 Modal -->
<div id="editReceivableModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">快速編輯應收帳款</h3>
            
            <!-- 專案和客戶資訊 -->
            <div class="mb-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
                <div class="text-gray-700 dark:text-gray-300">
                    <strong>專案：</strong>{{ $project->name }}
                </div>
                <div class="text-gray-600 dark:text-gray-400">
                    <strong>客戶：</strong>{{ $project->company->name ?? '-' }} | 
                    <strong>統編：</strong>{{ $project->company->tax_id ?? '-' }}
                </div>
            </div>
            
            <form id="editReceivableForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                收款日期 <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="receipt_date" id="edit_receipt_date" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                金額 <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="amount" id="edit_amount" step="1" min="0" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            內容說明
                        </label>
                        <textarea name="content" id="edit_content" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                發票日
                            </label>
                            <input type="date" name="due_date" id="edit_due_date"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                發票號碼
                            </label>
                            <input type="text" name="invoice_no" id="edit_invoice_no"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            備註
                        </label>
                        <textarea name="note" id="edit_note" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button"
                            onclick="document.getElementById('editReceivableModal').classList.add('hidden')"
                            class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
                        取消
                    </button>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
                        更新
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 快速編輯應付帳款 Modal -->
<div id="editPayableModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">快速編輯應付帳款</h3>
            
            <!-- 專案和客戶資訊 -->
            <div class="mb-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
                <div class="text-gray-700 dark:text-gray-300">
                    <strong>專案：</strong>{{ $project->name }}
                </div>
                <div class="text-gray-600 dark:text-gray-400">
                    <strong>客戶：</strong>{{ $project->company->name ?? '-' }} | 
                    <strong>統編：</strong>{{ $project->company->tax_id ?? '-' }}
                </div>
            </div>
            
            <form id="editPayableForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                付款日期 <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="payment_date" id="edit_payment_date" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                金額 <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="amount" id="edit_payable_amount" step="1" min="0" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            收款對象
                        </label>
                        <input type="text" name="vendor" id="edit_vendor"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            內容說明
                        </label>
                        <textarea name="content" id="edit_payable_content" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            備註
                        </label>
                        <textarea name="note" id="edit_payable_note" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button"
                            onclick="document.getElementById('editPayableModal').classList.add('hidden')"
                            class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
                        取消
                    </button>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
                        更新
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const receivablesData = @json($receivablesData);
const payablesData = @json($payablesData);

function openEditReceivableModal(id) {
    const receivable = receivablesData.find(r => r.id === id);
    if (!receivable) {
        console.error('Receivable not found:', id);
        return;
    }
    
    document.getElementById('edit_receipt_date').value = receivable.receipt_date || '';
    document.getElementById('edit_amount').value = receivable.amount || '';
    document.getElementById('edit_content').value = receivable.content || '';
    document.getElementById('edit_due_date').value = receivable.due_date || '';
    document.getElementById('edit_invoice_no').value = receivable.invoice_no || '';
    document.getElementById('edit_note').value = receivable.note || '';
    
    document.getElementById('editReceivableForm').action = `/receivables/${id}/quick-update`;
    document.getElementById('editReceivableModal').classList.remove('hidden');
}

function openEditPayableModal(id) {
    const payable = payablesData.find(p => p.id === id);
    if (!payable) {
        console.error('Payable not found:', id);
        return;
    }
    
    document.getElementById('edit_payment_date').value = payable.payment_date || '';
    document.getElementById('edit_payable_amount').value = payable.amount || '';
    document.getElementById('edit_vendor').value = payable.vendor || '';
    document.getElementById('edit_payable_content').value = payable.content || '';
    document.getElementById('edit_payable_note').value = payable.note || '';
    
    document.getElementById('editPayableForm').action = `/payables/${id}/quick-update`;
    document.getElementById('editPayableModal').classList.remove('hidden');
}

// 初始化專案標籤 Select2
$(document).ready(function() {
    $('#projectTags').select2({
        placeholder: '選擇標籤',
        allowClear: true,
        width: '100%',
        closeOnSelect: false,
        theme: 'default'
    });

    // 新增成員 modal：開啟時再初始化兩個 select2，避免 hidden 元素寬度為 0
    window.openAddMemberModal = function() {
        document.getElementById('addMemberModal').classList.remove('hidden');
        setTimeout(function() {
            // 成員選擇
            if ($('#memberUserSelect').hasClass('select2-hidden-accessible')) {
                $('#memberUserSelect').select2('destroy');
            }
            $('#memberUserSelect').select2({
                placeholder: '搜尋姓名或 Email',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addMemberModal')
            });
            // 職務選擇（tags 模式）
            if ($('#memberRoleSelect').hasClass('select2-hidden-accessible')) {
                $('#memberRoleSelect').select2('destroy');
            }
            $('#memberRoleSelect').select2({
                placeholder: '搜尋或輸入職務名稱...',
                allowClear: true,
                width: '100%',
                tags: true,
                dropdownParent: $('#addMemberModal'),
                language: {
                    noResults: function() { return '無相符職務，輸入後按 Enter 新增'; }
                },
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: term };
                }
            });
        }, 10);
    };
});
</script>

<!-- 操作按鈕 -->
<div class="mt-6 flex justify-between items-center">
    <form action="{{ route('tenant.projects.destroy', $project) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg">
            刪除
        </button>
    </form>

    <div class="flex gap-3">
        <a href="{{ route('tenant.projects.show', $project) }}" 
           class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
            編輯
        </a>
        <a href="{{ route('tenant.projects.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
            返回列表
        </a>
    </div>
</div>
@endsection
