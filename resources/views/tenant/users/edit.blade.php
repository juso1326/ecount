@extends('layouts.tenant')

@section('title', '編輯使用者')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">編輯使用者</h1>
</div>

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <form method="POST" action="{{ route('tenant.users.update', $user) }}">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- 角色與層級 -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">角色與權限</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <span class="text-red-500">*</span>角色層級
                        </label>
                        <select name="role" id="role" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                            <option value="">請選擇角色</option>
                            <option value="admin" {{ old('role', $currentRole) == 'admin' ? 'selected' : '' }}>系統管理員</option>
                            <option value="manager" {{ old('role', $currentRole) == 'manager' ? 'selected' : '' }}>總管理/主管</option>
                            <option value="accountant" {{ old('role', $currentRole) == 'accountant' ? 'selected' : '' }}>會計</option>
                            <option value="employee" {{ old('role', $currentRole) == 'employee' ? 'selected' : '' }}>成員</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="supervisor_field" style="display: {{ old('role', $currentRole) != 'admin' ? 'block' : 'none' }};">
                        <label for="supervisor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">上層主管</label>
                        <select name="supervisor_id" id="supervisor_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                            <option value="">請選擇</option>
                            @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $user->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- 基本資訊 -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">基本資訊</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="employee_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300">員工編號</label>
                        <input type="text" name="employee_no" id="employee_no" value="{{ old('employee_no', $user->employee_no) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <span class="text-red-500">*</span>姓名
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="short_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">簡稱</label>
                        <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $user->short_name) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">職位</label>
                        <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <span class="text-red-500">*</span>是否在職
                        </label>
                        <select name="is_active" id="is_active" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                            <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>是</option>
                            <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>否</option>
                        </select>
                    </div>

                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">部門</label>
                        <select name="department_id" id="department_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                            <option value="">請選擇</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- 帳號資訊 -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">帳號資訊</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <span class="text-red-500">*</span>登入帳號 (Email)
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            密碼 <span class="text-gray-500 text-xs">(留空表示不變更)</span>
                        </label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="backup_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備份 Email</label>
                        <input type="email" name="backup_email" id="backup_email" value="{{ old('backup_email', $user->backup_email) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>

            <!-- 個人資料 -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">個人資料</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">身分證字號</label>
                        <input type="text" name="id_number" id="id_number" value="{{ old('id_number', $user->id_number) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">出生年月日</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">電話 (市話)</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">手機</label>
                        <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $user->mobile) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>

            <!-- 銀行資訊 -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">銀行帳戶資訊</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">銀行</label>
                        <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $user->bank_name) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="bank_branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">分行</label>
                        <input type="text" name="bank_branch" id="bank_branch" value="{{ old('bank_branch', $user->bank_branch) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="bank_account" class="block text-sm font-medium text-gray-700 dark:text-gray-300">帳號</label>
                        <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account', $user->bank_account) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>

            <!-- 緊急聯絡人 -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">緊急聯絡人</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">緊急聯絡人姓名</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">緊急聯絡電話</label>
                        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>

            <!-- 任職資訊 -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">任職資訊</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">到職日</label>
                        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $user->hire_date?->format('Y-m-d')) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="resign_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">離職日</label>
                        <input type="date" name="resign_date" id="resign_date" value="{{ old('resign_date', $user->resign_date?->format('Y-m-d')) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="suspend_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">停權日</label>
                        <input type="date" name="suspend_date" id="suspend_date" value="{{ old('suspend_date', $user->suspend_date?->format('Y-m-d')) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>

            <!-- 備註 -->
            <div>
                <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備註</label>
                <textarea name="note" id="note" rows="3"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary">{{ old('note', $user->note) }}</textarea>
            </div>
        </div>

        <!-- 按鈕 -->
        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('tenant.users.index') }}" 
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                取消
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                更新
            </button>
        </div>
    </form>
</div>

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
