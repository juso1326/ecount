<!-- 個人基本資訊 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
        個人基本資訊
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- 角色層級 -->
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                角色層級 <span class="text-red-500">*</span>
            </label>
            <select name="role" id="role" required
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">請選擇角色</option>
                <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>系統管理員</option>
                <option value="manager" {{ old('role', $user->role ?? '') == 'manager' ? 'selected' : '' }}>總管理/主管</option>
                <option value="accountant" {{ old('role', $user->role ?? '') == 'accountant' ? 'selected' : '' }}>會計</option>
                <option value="employee" {{ old('role', $user->role ?? '') == 'employee' ? 'selected' : '' }}>成員</option>
            </select>
            @error('role')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- 上層主管 -->
        <div id="supervisor_field" style="{{ (old('role', $user->role ?? '') && old('role', $user->role ?? '') !== 'admin') ? '' : 'display: none;' }}">
            <label for="supervisor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">上層主管</label>
            <select name="supervisor_id" id="supervisor_id"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">請選擇</option>
                @foreach($supervisors as $supervisor)
                    <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $user->supervisor_id ?? '') == $supervisor->id ? 'selected' : '' }}>
                        {{ $supervisor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- 員工編號 -->
        <div>
            <label for="employee_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300">員工編號</label>
            <input type="text" name="employee_no" id="employee_no" value="{{ old('employee_no', $user->employee_no ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

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

        <!-- 職位 -->
        <div>
            <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">職位</label>
            <input type="text" name="position" id="position" value="{{ old('position', $user->position ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 部門 -->
        <div>
            <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">部門</label>
            <select name="department_id" id="department_id"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">請選擇</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id ?? '') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- 是否在職 -->
        <div>
            <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                是否在職 <span class="text-red-500">*</span>
            </label>
            <select name="is_active" id="is_active" required
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="1" {{ old('is_active', $user->is_active ?? 1) ? 'selected' : '' }}>是</option>
                <option value="0" {{ !old('is_active', $user->is_active ?? 1) ? 'selected' : '' }}>否</option>
            </select>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                登入帳號 (Email) <span class="text-red-500">*</span>
            </label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" {{ isset($user) ? '' : 'required' }}
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            @error('email')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        @if(!isset($user))
        <!-- 密碼 (僅新增時) -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                密碼 <span class="text-red-500">*</span>
            </label>
            <input type="password" name="password" id="password" required
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            @error('password')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        @endif

        <!-- 備份 Email -->
        <div>
            <label for="backup_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備份 Email</label>
            <input type="email" name="backup_email" id="backup_email" value="{{ old('backup_email', $user->backup_email ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 身分證字號 -->
        <div>
            <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">身分證字號</label>
            <input type="text" name="id_number" id="id_number" value="{{ old('id_number', $user->id_number ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 出生年月日 -->
        <div>
            <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">出生年月日</label>
            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 電話 -->
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">電話 (市話)</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 手機 -->
        <div>
            <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">手機</label>
            <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $user->mobile ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 銀行 -->
        <div>
            <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">銀行</label>
            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $user->bank_name ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 分行 -->
        <div>
            <label for="bank_branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">分行</label>
            <input type="text" name="bank_branch" id="bank_branch" value="{{ old('bank_branch', $user->bank_branch ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 帳號 -->
        <div>
            <label for="bank_account" class="block text-sm font-medium text-gray-700 dark:text-gray-300">帳號</label>
            <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account', $user->bank_account ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 備註 -->
        <div class="md:col-span-2">
            <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備註</label>
            <textarea name="note" id="note" rows="3"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('note', $user->note ?? '') }}</textarea>
        </div>
    </div>
</div>
