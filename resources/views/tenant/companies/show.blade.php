@extends('layouts.tenant')

@section('title', '查看客戶/廠商')

@section('page-title', '客戶/廠商詳情')

@section('content')
<!-- 頁面標題與按鈕 -->
<div class="mb-3 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">客戶/廠商詳情</h1>
    <div class="flex gap-3">
        <a href="{{ route('tenant.companies.edit', $company) }}" 
           class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
            編輯
        </a>
        <a href="{{ route('tenant.companies.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
            返回列表
        </a>
    </div>
</div>

<!-- 內容區域 - 左右佈局 -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
    <!-- 左側：基本資訊和聯絡資訊 (2/3寬度) -->
    <div class="lg:col-span-2 space-y-3">
        <!-- 基本資訊 -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 pb-3 border-b border-gray-200 dark:border-gray-700">
                基本資訊
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">公司代碼</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->code }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">名稱</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->name }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">簡稱</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->short_name ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">類型</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $company->type === 'company' ? '公司' : '個人' }}
                        </span>
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">屬性</label>
                    <div class="mt-1 flex gap-2">
                        @if($company->is_client)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                客戶
                            </span>
                        @endif
                        @if($company->is_outsource)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                外製
                            </span>
                        @endif
                        @if(!$company->is_client && !$company->is_outsource)
                            <span class="text-gray-500 dark:text-gray-400">-</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">統一編號</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->tax_id ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">狀態</label>
                    <p class="mt-1">
                        @if($company->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                啟用
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                停用
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- 聯絡資訊 -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 pb-3 border-b border-gray-200 dark:border-gray-700">
                聯絡資訊
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">聯絡人</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->contact_name ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">聯絡電話</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->phone ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">傳真</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->fax ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->email ?? '-' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">地址</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->address ?? '-' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">網站</label>
                    <p class="mt-1 text-gray-900 dark:text-white">
                        @if($company->website)
                            <a href="{{ $company->website }}" target="_blank" class="text-primary hover:underline">
                                {{ $company->website }}
                            </a>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- 備註區域 -->
        @if($company->note)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 pb-3 border-b border-gray-200 dark:border-gray-700">
                備註
            </h2>
            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $company->note }}</div>
        </div>
        @endif

        <!-- 系統資訊 -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 pb-3 border-b border-gray-200 dark:border-gray-700">
                系統資訊
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">建立時間</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->created_at->format('Y-m-d H:i:s') }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">最後更新</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $company->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 右側：相關專案 (1/3寬度) -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 pb-3 border-b border-gray-200 dark:border-gray-700">
                相關專案 ({{ $company->projects->count() }})
            </h2>
            
            @if($company->projects->count() > 0)
                <div class="space-y-3">
                    @foreach($company->projects as $project)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</h3>
                            @if($project->status === 'in_progress')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    進行中
                                </span>
                            @elseif($project->status === 'completed')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    已完成
                                </span>
                            @elseif($project->status === 'planning')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    規劃中
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $project->status }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                            <p>代碼：{{ $project->code }}</p>
                            <p>預算：{{ number_format($project->budget, 0) }}</p>
                            @if($project->manager)
                                <p>負責人：{{ $project->manager->name }}</p>
                            @endif
                        </div>
                        
                        <a href="{{ route('tenant.projects.show', $project) }}" 
                           class="mt-3 inline-block text-primary hover:text-primary-dark text-sm font-medium">
                            查看詳情 →
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">尚無相關專案</p>
            @endif
        </div>
    </div>
</div>

<!-- 操作按鈕 -->
<div class="mt-6 flex justify-between items-center">
    <form action="{{ route('tenant.companies.destroy', $company) }}" method="POST" 
          onsubmit="return confirm('確定要刪除此客戶/廠商嗎？此操作無法復原。');">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg">
            刪除
        </button>
    </form>

    <div class="flex gap-3">
        <a href="{{ route('tenant.companies.edit', $company) }}" 
           class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg shadow-sm">
            編輯
        </a>
        <a href="{{ route('tenant.companies.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg">
            返回列表
        </a>
    </div>
</div>
@endsection
