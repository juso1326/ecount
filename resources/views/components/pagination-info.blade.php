@props(['paginator'])

<div class="mt-6">
    <!-- 分頁資訊 -->
    <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
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
    
    <!-- 分頁導航 -->
    @if($paginator->hasPages())
        {{ $paginator->appends(request()->except('page'))->links() }}
    @endif
</div>
