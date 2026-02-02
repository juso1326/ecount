@extends('layouts.tenant')

@section('title', '編輯使用者')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">專案帳戶管理 &gt; 使用者管理 &gt; 編輯使用者</p>
</div>

<!-- 頁面標題 -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">編輯使用者</h1>
</div>

<form method="POST" action="{{ route('tenant.users.update', $user) }}">
    @csrf
    @method('PUT')
    
    @include('tenant.users._form')

    <div class="mt-6 flex justify-end space-x-3">
        <a href="{{ route('tenant.users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 font-bold py-2 px-6 rounded-lg">
            取消
        </a>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
            更新
        </button>
    </div>
</form>

<script>
// 根據角色顯示/隱藏上層主管欄位
document.getElementById('role').addEventListener('change', function() {
    const supervisorField = document.getElementById('supervisor_field');
    if (this.value && this.value !== 'admin') {
        supervisorField.style.display = 'block';
    } else {
        supervisorField.style.display = 'none';
    }
});
</script>

@endsection
