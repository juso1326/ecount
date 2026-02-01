@extends('layouts.tenant')

@section('title', $categoryName . ' - 代碼管理')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            {{ $categoryName }}
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li><a class="font-medium" href="{{ route('tenant.dashboard') }}">儀表板 /</a></li>
                <li><a class="font-medium" href="{{ route('tenant.codes.index') }}">代碼管理 /</a></li>
                <li class="font-medium text-primary">{{ $categoryName }}</li>
            </ol>
        </nav>
    </div>

    <!-- Actions Bar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-3">
            <!-- Search -->
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="搜尋代碼或名稱..."
                       class="rounded border-stroke bg-white px-4 py-2 text-black dark:border-strokedark dark:bg-boxdark dark:text-white">
                <select name="status" class="rounded border-stroke bg-white px-4 py-2 text-black dark:border-strokedark dark:bg-boxdark dark:text-white">
                    <option value="">全部狀態</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>啟用</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>停用</option>
                </select>
                <button type="submit" class="rounded bg-primary px-4 py-2 text-white hover:bg-opacity-90">
                    搜尋
                </button>
            </form>
        </div>
        <div>
            <a href="{{ route('tenant.codes.create', $category) }}" 
               class="inline-flex items-center justify-center rounded-md bg-primary px-6 py-3 text-center font-medium text-white hover:bg-opacity-90">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                新增代碼
            </a>
        </div>
    </div>

    <!-- Data Table -->
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="px-4 py-6 md:px-6 xl:px-7.5">
            <h4 class="text-xl font-semibold text-black dark:text-white">
                代碼列表 (共 {{ $codes->total() }} 筆)
            </h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="px-4 py-4 font-medium text-black dark:text-white xl:pl-11">#</th>
                        <th class="px-4 py-4 font-medium text-black dark:text-white">代碼</th>
                        <th class="px-4 py-4 font-medium text-black dark:text-white">名稱</th>
                        <th class="px-4 py-4 font-medium text-black dark:text-white">排序</th>
                        <th class="px-4 py-4 font-medium text-black dark:text-white">狀態</th>
                        <th class="px-4 py-4 font-medium text-black dark:text-white">說明</th>
                        <th class="px-4 py-4 font-medium text-black dark:text-white">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($codes as $index => $code)
                    <tr class="border-b border-stroke dark:border-strokedark">
                        <td class="px-4 py-5 pl-9 xl:pl-11">
                            <p class="text-sm">{{ $codes->firstItem() + $index }}</p>
                        </td>
                        <td class="px-4 py-5">
                            <p class="font-medium text-black dark:text-white">{{ $code->code }}</p>
                        </td>
                        <td class="px-4 py-5">
                            <p class="text-black dark:text-white">{{ $code->name }}</p>
                        </td>
                        <td class="px-4 py-5">
                            <p class="text-black dark:text-white">{{ $code->sort_order }}</p>
                        </td>
                        <td class="px-4 py-5">
                            @if($code->is_active)
                                <span class="inline-flex rounded-full bg-success bg-opacity-10 px-3 py-1 text-sm font-medium text-success">啟用</span>
                            @else
                                <span class="inline-flex rounded-full bg-danger bg-opacity-10 px-3 py-1 text-sm font-medium text-danger">停用</span>
                            @endif
                        </td>
                        <td class="px-4 py-5">
                            <p class="text-sm text-black dark:text-white">{{ $code->description ?: '-' }}</p>
                        </td>
                        <td class="px-4 py-5">
                            <div class="flex items-center space-x-3.5">
                                <a href="{{ route('tenant.codes.edit', [$category, $code]) }}" 
                                   class="hover:text-primary" title="編輯">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('tenant.codes.destroy', [$category, $code]) }}" 
                                      onsubmit="return confirm('確定要刪除此代碼嗎？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hover:text-danger" title="刪除">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-5 text-center">
                            <p class="text-black dark:text-white">目前沒有代碼資料</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($codes->hasPages())
        <div class="px-4 py-6 md:px-6 xl:px-7.5">
            {{ $codes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
