@extends('layouts.tenant')

@section('title', '編輯使用者')

@section('content')
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">專案帳戶管理 &gt; 使用者管理 &gt; 編輯使用者</p>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">編輯使用者</h1>
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

@endsection

        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 角色層級 -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    角色層級 <span class="text-red-500">*</span>
                </label>
                <select name="role" id="role" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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

            <!-- 上層主管 -->
            <div id="supervisor_field" style="display: {{ old('role', $currentRole) != 'admin' ? 'block' : 'none' }};">
                <label for="supervisor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">上層主管</label>
                <select name="supervisor_id" id="supervisor_id"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">請選擇</option>
                    @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $user->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                            {{ $supervisor->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 員工編號 -->
            <div>
                <label for="employee_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300">員工編號</label>
                <input type="text" name="employee_no" id="employee_no" value="{{ old('employee_no', $user->employee_no) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 姓名 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    姓名 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 簡稱 -->
            <div>
                <label for="short_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">簡稱</label>
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $user->short_name) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 職位 -->
            <div>
                <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">職位</label>
                <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 是否在職 -->
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    是否在職 <span class="text-red-500">*</span>
                </label>
                <select name="is_active" id="is_active" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>是</option>
                    <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>否</option>
                </select>
            </div>

            <!-- 部門 -->
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">部門</label>
                <select name="department_id" id="department_id"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">請選擇</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    登入帳號 (Email) <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 密碼 -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    密碼 <span class="text-xs text-gray-500">(留空表示不變更)</span>
                </label>
                <input type="password" name="password" id="password"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('password')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 備份 Email -->
            <div>
                <label for="backup_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備份 Email</label>
                <input type="email" name="backup_email" id="backup_email" value="{{ old('backup_email', $user->backup_email) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 身分證字號 -->
            <div>
                <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">身分證字號</label>
                <input type="text" name="id_number" id="id_number" value="{{ old('id_number', $user->id_number) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 出生年月日 -->
            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">出生年月日</label>
                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 電話 -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">電話 (市話)</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 手機 -->
            <div>
                <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">手機</label>
                <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $user->mobile) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 銀行 -->
            <div>
                <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">銀行</label>
                <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $user->bank_name) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 分行 -->
            <div>
                <label for="bank_branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">分行</label>
                <input type="text" name="bank_branch" id="bank_branch" value="{{ old('bank_branch', $user->bank_branch) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 帳號 -->
            <div>
                <label for="bank_account" class="block text-sm font-medium text-gray-700 dark:text-gray-300">帳號</label>
                <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account', $user->bank_account) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 緊急聯絡人 -->
            <div>
                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">緊急聯絡人姓名</label>
                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 緊急聯絡電話 -->
            <div>
                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">緊急聯絡電話</label>
                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 到職日 -->
            <div>
                <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">到職日</label>
                <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $user->hire_date?->format('Y-m-d')) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 離職日 -->
            <div>
                <label for="resign_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">離職日</label>
                <input type="date" name="resign_date" id="resign_date" value="{{ old('resign_date', $user->resign_date?->format('Y-m-d')) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 停權日 -->
            <div>
                <label for="suspend_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">停權日</label>
                <input type="date" name="suspend_date" id="suspend_date" value="{{ old('suspend_date', $user->suspend_date?->format('Y-m-d')) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- 備註 -->
            <div class="md:col-span-2">
                <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備註</label>
                <textarea name="note" id="note" rows="3"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('note', $user->note) }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('tenant.users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 font-bold py-2 px-4 rounded">
                取消
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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
