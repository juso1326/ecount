@props(['paginator', 'exportRoute' => null])

<div class="flex items-center justify-between mt-6">
    <!-- 分頁資訊 -->
    <div class="text-sm text-gray-600 dark:text-gray-400">
        @if($paginator->total() > 0)
            顯示第 <span class="font-medium">{{ $paginator->firstItem() }}</span> 
            到 <span class="font-medium">{{ $paginator->lastItem() }}</span> 筆，
            共 <span class="font-medium">{{ number_format($paginator->total()) }}</span> 筆
            （每頁 {{ $paginator->perPage() }} 筆，
            第 {{ $paginator->currentPage() }}/{{ $paginator->lastPage() }} 頁）
        @else
            無資料
        @endif
    </div>
    
    <!-- 匯出按鈕 -->
    @if($exportRoute && $paginator->total() > 0)
    <div>
        <a href="{{ $exportRoute }}" 
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            匯出 Excel
        </a>
    </div>
    @endif
</div>

<!-- 分頁導航 -->
@if($paginator->hasPages())
<div class="mt-4">
    {{ $paginator->appends(request()->except('page'))->links() }}
</div>
@endif
