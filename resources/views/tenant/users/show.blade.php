@extends('layouts.tenant')

@section('title', '使用者詳情')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">使用者詳情</h1>
    <div class="space-x-2">
        <a href="{{ route('tenant.users.edit', $user) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            編輯
        </a>
        <a href="{{ route('tenant.users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
            返回列表
        </a>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-500">姓名</label>
            <p class="mt-1 text-lg text-gray-900">{{ $user->name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">Email</label>
            <p class="mt-1 text-lg text-gray-900">{{ $user->email }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">狀態</label>
            <p class="mt-1">
                @if($user->is_active)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        啟用
                    </span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        停用
                    </span>
                @endif
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">最後登入時間</label>
            <p class="mt-1 text-lg text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : '從未登入' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">建立時間</label>
            <p class="mt-1 text-lg text-gray-900">{{ $user->created_at->format('Y-m-d H:i:s') }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">更新時間</label>
            <p class="mt-1 text-lg text-gray-900">{{ $user->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</div>

@if($user->id !== auth()->id())
<div class="mt-6 flex justify-end">
    <form action="{{ route('tenant.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('確定要刪除此使用者嗎？');">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
            刪除使用者
        </button>
    </form>
</div>
@endif
@endsection
