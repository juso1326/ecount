@extends('layouts.tenant')

@section('title', '編輯角色')

@section('page-title', '編輯角色')

@section('content')
<form action="{{ route('tenant.roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">基本資訊</h2>
        
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                角色名稱 <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   value="{{ old('name', $role->name) }}"
                   required
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            @error('name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">權限設定</h2>
            <div class="space-x-2">
                <button type="button" onclick="selectAllPermissions()"
                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium">
                    全選
                </button>
                <button type="button" onclick="deselectAllPermissions()"
                        class="px-3 py-1 bg-gray-400 hover:bg-gray-500 text-white rounded text-sm font-medium">
                    取消全選
                </button>
            </div>
        </div>
        
        <div class="space-y-6">
            @foreach($permissions as $module => $perms)
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0">
                <div class="flex items-center mb-3">
                    <input type="checkbox" 
                           id="module_{{ $module }}" 
                           class="module-checkbox rounded border-gray-300 text-primary focus:ring-primary"
                           data-module="{{ $module }}">
                    <label for="module_{{ $module }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ \App\Helpers\PermissionHelper::getModuleName($module) }} (全選)
                    </label>
                </div>
                
                <div class="ml-6 grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($perms as $permission)
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="permissions[]" 
                               id="perm_{{ $permission->id }}" 
                               value="{{ $permission->name }}"
                               class="permission-checkbox rounded border-gray-300 text-primary focus:ring-primary"
                               data-module="{{ $module }}"
                               {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                        <label for="perm_{{ $permission->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ \App\Helpers\PermissionHelper::getActionName(explode('.', $permission->name)[1]) }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('tenant.roles.index') }}" 
           class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition">
            取消
        </a>
        <button type="submit" 
                class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition">
            更新角色
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
        const module = moduleCheckbox.dataset.module;
        const permCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
        
        moduleCheckbox.addEventListener('change', function() {
            permCheckboxes.forEach(cb => cb.checked = this.checked);
        });
        
        function updateModuleCheckbox() {
            const checkedCount = Array.from(permCheckboxes).filter(cb => cb.checked).length;
            moduleCheckbox.checked = checkedCount === permCheckboxes.length;
            moduleCheckbox.indeterminate = checkedCount > 0 && checkedCount < permCheckboxes.length;
        }
        
        permCheckboxes.forEach(cb => cb.addEventListener('change', updateModuleCheckbox));
        updateModuleCheckbox();
    });
});

function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = true;
    });
    // 更新模塊複選框
    document.querySelectorAll('.module-checkbox').forEach(cb => {
        cb.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = false;
    });
    // 更新模塊複選框
    document.querySelectorAll('.module-checkbox').forEach(cb => {
        cb.checked = false;
        cb.indeterminate = false;
    });
}
</script>
@endsection
