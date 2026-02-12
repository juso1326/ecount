@extends('layouts.tenant')

@section('title', '薪資明細')

@section('page-title', '薪資明細')

@section('content')
<!-- 頁面標題與導航 -->
<div class="mb-3">
    <!-- 月份導航 -->
    <div class="flex items-center justify-between mb-2">
        <a href="{{ route('tenant.salaries.show', ['user' => $user->id, 'year' => $year, 'month' => $month, 'nav' => 'prev']) }}" 
           class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            上個月
        </a>
        
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $salary['period']['label'] }}</h2>
        
        <a href="{{ route('tenant.salaries.show', ['user' => $user->id, 'year' => $year, 'month' => $month, 'nav' => 'next']) }}" 
           class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition">
            下個月
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    
    <!-- 標題與操作按鈕 -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }} 薪資明細</h1>
        <div class="flex gap-2">
            @if(!$isPaid && $salary['total'] > 0)
            <button onclick="document.getElementById('payModal').classList.remove('hidden')"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                確認撥款
            </button>
            @endif
        </div>
    </div>
</div>

<!-- 薪資摘要 -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">基本薪資</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">
            ${{ number_format($salary['base_salary'], 0) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">加項</div>
        <div class="text-2xl font-bold text-green-600">
            +${{ number_format($salary['additions'], 0) }}
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">扣項</div>
        <div class="text-2xl font-bold text-red-600">
            -${{ number_format($salary['deductions'], 0) }}
        </div>
    </div>
    <div class="bg-blue-50 dark:bg-blue-900 shadow-sm rounded-lg border border-blue-200 dark:border-blue-700 p-4">
        <div class="text-sm text-blue-600 dark:text-blue-300">總計</div>
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-300">
            ${{ number_format($salary['total'], 0) }}
        </div>
    </div>
</div>

<!-- 薪資明細 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-3">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">薪資項目</h2>
    </div>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">日期</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">專案</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">內容</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">金額</th>
                @if(!$isPaid)
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">移動</th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($salary['items'] as $item)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $item->payment_date->format('Y/m/d') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                    {{ $item->project->name ?? '-' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                    {{ $item->content ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                    ${{ number_format($item->amount, 0) }}
                </td>
                @if(!$isPaid && !$item->is_salary_paid)
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                    <div class="flex justify-center gap-2">
                        <button onclick="moveItem({{ $item->id }}, 'prev')" 
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                title="移到上個月">
                            &lt;
                        </button>
                        <button onclick="moveItem({{ $item->id }}, 'next')" 
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                title="移到下個月">
                            &gt;
                        </button>
                    </div>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ !$isPaid ? 5 : 4 }}" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">無薪資項目</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 撥款彈窗 -->
<div id="payModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-2">確認薪資撥款</h3>
            <form action="{{ route('tenant.salaries.pay', $user) }}" method="POST">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">
                
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">應付總額</label>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        ${{ number_format($salary['total'], 0) }}
                    </div>
                </div>
                
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">實發金額 *</label>
                    <input type="number" name="actual_amount" 
                           value="{{ $salary['total'] }}"
                           step="0.01" min="0"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"
                           required>
                </div>
                
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">備註</label>
                    <textarea name="remark" rows="3"
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"></textarea>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        確認撥款
                    </button>
                    <button type="button" onclick="document.getElementById('payModal').classList.add('hidden')"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        取消
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function moveItem(payableId, direction) {
    if (!confirm('確定要移動此薪資項目到' + (direction === 'prev' ? '上個月' : '下個月') + '嗎？')) {
        return;
    }
    
    const url = direction === 'prev' 
        ? '{{ route("tenant.salaries.move-prev") }}'
        : '{{ route("tenant.salaries.move-next") }}';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ payable_id: payableId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('錯誤：' + data.message);
        }
    })
    .catch(error => {
        alert('操作失敗，請稍後再試');
        console.error('Error:', error);
    });
}
</script>
@endsection
