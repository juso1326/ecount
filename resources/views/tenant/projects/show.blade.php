@extends('layouts.tenant')

@section('title', '專案詳情')

@section('page-title', '專案詳情')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.projects.index') }}" class="hover:text-primary">專案帳戶管理 &gt; 專案管理</a>
        &gt; 查看
    </p>
</div>

<!-- 頁面標題與按鈕 -->
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">專案詳情</h1>
    <div class="flex gap-3">
        <a href="{{ route('tenant.projects.edit', $project) }}" 
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
    <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded">
        {{ session('error') }}
    </div>
@endif

<!-- 內容區域 - 左右佈局 -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- 左側：專案資訊 (2/3寬度) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- 基本資訊 -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                基本資訊
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">專案代碼</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $project->code }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">專案名稱</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $project->name }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">所屬客戶</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $project->company?->name ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">負責部門</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $project->department?->name ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">專案經理</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $project->manager?->name ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">狀態</label>
                    <p class="mt-1">
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
                        @elseif($project->status === 'on_hold')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                暫停
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                已取消
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">開始日期</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $project->start_date?->format('Y-m-d') ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">結束日期</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $project->end_date?->format('Y-m-d') ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">預算金額</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ number_format($project->budget, 0) }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">實際成本</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ number_format($project->actual_cost, 0) }}</p>
                </div>
            </div>
        </div>

        <!-- 專案描述 -->
        @if($project->description)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                專案描述
            </h2>
            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $project->description }}</div>
        </div>
        @endif

        <!-- 備註 -->
        @if($project->note)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                備註
            </h2>
            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $project->note }}</div>
        </div>
        @endif

        <!-- 系統資訊 -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                系統資訊
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">建立時間</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $project->created_at->format('Y-m-d H:i:s') }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">最後更新</label>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $project->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 右側：專案成員 (1/3寬度) -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    專案成員 ({{ $project->members->count() }})
                </h2>
                <button onclick="document.getElementById('addMemberModal').classList.remove('hidden')"
                        class="text-primary hover:text-primary-dark">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
            
            @if($project->members->count() > 0)
                <div class="space-y-3">
                    @foreach($project->members as $member)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $member->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->email }}</p>
                                @if($member->pivot->role)
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $member->pivot->role }}</p>
                                @endif
                            </div>
                            <form action="{{ route('tenant.projects.members.remove', [$project, $member]) }}" 
                                  method="POST"
                                  onsubmit="return confirm('確定要移除此成員嗎？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        
                        <!-- 成員的專案列表 -->
                        @php
                            $memberProjects = $member->projects()->where('projects.id', '!=', $project->id)->where('status', 'in_progress')->get();
                        @endphp
                        @if($memberProjects->count() > 0)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">其他進行中專案：</p>
                                <div class="space-y-1">
                                    @foreach($memberProjects as $mp)
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            • {{ $mp->name }}
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">尚無專案成員</p>
            @endif
        </div>
    </div>
</div>

<!-- 新增成員 Modal -->
<div id="addMemberModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">新增專案成員</h3>
            
            <form action="{{ route('tenant.projects.members.add', $project) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        選擇成員 <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
                        <option value="">請選擇</option>
                        @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $user)
                            @if(!$project->members->contains($user->id))
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        角色/職務
                    </label>
                    <input type="text" name="role" 
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"
                           placeholder="例如：前端開發、後端開發">
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

<!-- 操作按鈕 -->
<div class="mt-6 flex justify-between items-center">
    <form action="{{ route('tenant.projects.destroy', $project) }}" method="POST" 
          onsubmit="return confirm('確定要刪除此專案嗎？此操作無法復原。');">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg">
            刪除
        </button>
    </form>

    <div class="flex gap-3">
        <a href="{{ route('tenant.projects.edit', $project) }}" 
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
