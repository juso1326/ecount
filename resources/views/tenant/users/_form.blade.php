<!-- 基本資訊 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">基本資訊</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 姓名 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    姓名 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 簡稱 -->
            <div>
                <label for="short_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">簡稱</label>
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $user->short_name ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    登入帳號 (Email) <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 密碼 -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    密碼 @if(!isset($user))<span class="text-red-500">*</span>@endif
                </label>
                <input type="password" name="password" id="password" {{ !isset($user) ? 'required' : '' }}
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @if(isset($user))
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">留空表示不修改密碼</p>
                @endif
                @error('password')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 備份 Email -->
            <div class="md:col-span-2">
                <label for="backup_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備份 Email</label>
                <input type="email" name="backup_email" id="backup_email" value="{{ old('backup_email', $user->backup_email ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>
</div>

<!-- 角色與權限 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">角色與權限</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 角色層級 -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    角色層級 <span class="text-red-500">*</span>
                </label>
                <select name="role" id="role" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">請選擇角色</option>
                    <option value="admin" {{ old('role', $currentRole ?? '') == 'admin' ? 'selected' : '' }}>系統管理員</option>
                    <option value="manager" {{ old('role', $currentRole ?? '') == 'manager' ? 'selected' : '' }}>總管理/主管</option>
                    <option value="accountant" {{ old('role', $currentRole ?? '') == 'accountant' ? 'selected' : '' }}>會計</option>
                    <option value="employee" {{ old('role', $currentRole ?? '') == 'employee' ? 'selected' : '' }}>成員</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 關聯成員 -->
            <div>
                <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">關聯成員（員工）</label>
                <select name="company_id" id="company_id"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">請選擇</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ old('company_id', $user->company_id ?? '') == $member->id ? 'selected' : '' }}>
                            {{ $member->name }} ({{ $member->code }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">選擇此使用者對應的員工資料</p>
            </div>

            <!-- 是否啟動 -->
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    是否啟動 <span class="text-red-500">*</span>
                </label>
                <select name="is_active" id="is_active" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="1" {{ old('is_active', $user->is_active ?? 1) ? 'selected' : '' }}>是</option>
                    <option value="0" {{ !old('is_active', $user->is_active ?? 1) ? 'selected' : '' }}>否</option>
                </select>
            </div>

            <!-- 權限開始日期 -->
            <div>
                <label for="permission_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">權限開始日期</label>
                <input type="date" name="permission_start_date" id="permission_start_date" value="{{ old('permission_start_date', isset($user->permission_start_date) ? $user->permission_start_date->format('Y-m-d') : '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 權限結束日期 -->
            <div class="md:col-span-2">
                <label for="permission_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">權限結束日期</label>
                <input type="date" name="permission_end_date" id="permission_end_date" value="{{ old('permission_end_date', isset($user->permission_end_date) ? $user->permission_end_date->format('Y-m-d') : '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>
</div>

<!-- 備註 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">備註</h3>
    </div>
    <div class="p-6">
        <textarea name="note" id="note" rows="4"
            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('note', $user->note ?? '') }}</textarea>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 監聽角色選擇變化
    const roleSelect = document.getElementById('role');
    const supervisorField = document.getElementById('supervisor_field');
    
    if (roleSelect && supervisorField) {
        roleSelect.addEventListener('change', function() {
            if (this.value === 'admin') {
                supervisorField.style.display = 'none';
                document.getElementById('supervisor_id').value = '';
            } else {
                supervisorField.style.display = 'block';
            }
        });
    }
});
</script>
