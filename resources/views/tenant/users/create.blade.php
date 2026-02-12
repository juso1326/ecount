@extends('layouts.tenant')

@section('title', '新增使用者')

@section('content')
<!-- 頁面標題 -->
<div class="mb-3">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">新增使用者</h1>
</div>

<form method="POST" action="{{ route('tenant.users.store') }}">
    @csrf
    
    @include('tenant.users._form')

    <div class="mt-6 flex justify-end space-x-3">
        <a href="{{ route('tenant.users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 font-bold py-2 px-6 rounded-lg">
            取消
        </a>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
            新增
        </button>
    </div>
</form>

@endsection
