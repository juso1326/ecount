@extends('layouts.tenant')

@section('title', '應收帳款管理')

@section('page-title', '應收帳款管理')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">財務管理 &gt; 應收帳款管理</p>
</div>

<!-- 頁面標題與按鈕 -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">應收帳款管理</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            第 {{ $receivables->currentPage() }} / {{ $receivables->lastPage() }} 頁，每頁15筆，共{{ $receivables->total() }}筆
        </p>
    </div>
    <a href="{{ route('tenant.receivables.create') }}" 
       class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
        + 新增應收帳款
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- 搜尋與篩選 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <form method="GET" action="{{ route('tenant.receivables.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- 搜尋框 -->
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="搜尋單號、專案代碼/名稱、廠商、內容..." 
               class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
        
        <!-- 專案篩選 -->
        <select name="project_id" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">全部專案</option>
            @foreach(\App\Models\Project::where('is_active', true)->orderBy('code')->get() as $project)
                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                    {{ $project->code }} - {{ $project->name }}
                </option>
            @endforeach
        </select>
        
        <!-- 客戶篩選 -->
        <select name="company_id" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">全部客戶</option>
            @foreach(\App\Models\Company::where('is_active', true)->orderBy('name')->get() as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
            @endforeach
        </select>
        
        <!-- 狀態篩選 -->
        <select name="status" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">全部狀態</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>待收款</option>
            <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>部分收款</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>已收款</option>
            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>逾期</option>
        </select>

        <!-- 年份 -->
        <select name="year" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">全部年份</option>
            @for($y = now()->year; $y >= 2020; $y--)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}年</option>
            @endfor
        </select>

        <!-- 月份 -->
        <select name="month" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">全部月份</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}月</option>
            @endfor
        </select>
        
        <!-- 搜尋按鈕 -->
        <div class="md:col-span-6 flex gap-2">
            <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg flex-1">
                搜尋
            </button>
            @if(request()->hasAny(['search', 'project_id', 'company_id', 'status', 'year', 'month']))
                <a href="{{ route('tenant.receivables.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
                    清除
                </a>
            @endif
        </div>
    </form>
</div>

<!-- 資料表格 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">收款日期</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">單號</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">專案/客戶</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">內容</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">應收金額</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">已收金額</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">未收金額</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">狀態</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($receivables as $receivable)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $receivable->receipt_date->format('Y/m/d') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $receivable->receipt_no }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        @if($receivable->project)
                            <div>{{ $receivable->project->code }}</div>
                        @endif
                        @if($receivable->company)
                            <div class="text-gray-500 dark:text-gray-400">{{ $receivable->company->name }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        {{ Str::limit($receivable->content, 30) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                        NT$ {{ number_format($receivable->amount, 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                        NT$ {{ number_format($receivable->received_amount ?? 0, 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ $receivable->remaining_amount > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400' }}">
                        NT$ {{ number_format($receivable->remaining_amount, 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($receivable->status === 'paid')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">已收款</span>
                        @elseif($receivable->status === 'partial')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">部分收款</span>
                        @elseif($receivable->status === 'overdue')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">逾期</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">待收款</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('tenant.receivables.show', $receivable) }}" 
                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">檢視</a>
                        <a href="{{ route('tenant.receivables.edit', $receivable) }}" 
                           class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">編輯</a>
                        <form action="{{ route('tenant.receivables.destroy', $receivable) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('確定要刪除此應收帳款嗎？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">刪除</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        目前沒有應收帳款資料
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($receivables->count() > 0)
        <tfoot class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100">
                    總計：
                </td>
                <td class="px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                    NT$ {{ number_format($totalAmount, 0) }}
                </td>
                <td class="px-6 py-3 text-right text-sm font-bold text-green-600 dark:text-green-400">
                    NT$ {{ number_format($totalReceived, 0) }}
                </td>
                <td class="px-6 py-3 text-right text-sm font-bold text-red-600 dark:text-red-400">
                    NT$ {{ number_format($totalAmount - $totalReceived, 0) }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<!-- 分頁 -->
<div class="mt-6">
    {{ $receivables->withQueryString()->links() }}
</div>
@endsection
