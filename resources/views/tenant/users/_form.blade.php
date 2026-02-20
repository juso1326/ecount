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
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 簡稱 -->
            <div>
                <label for="short_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">簡稱</label>
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $user->short_name ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    登入帳號 (Email) <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
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
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                @if(isset($user))
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">留空表示不修改密碼</p>
                @endif
                @error('password')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 備份 Email -->
            <div>
                <label for="backup_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備份 Email</label>
                <input type="email" name="backup_email" id="backup_email" value="{{ old('backup_email', $user->backup_email ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>
        </div>
    </div>
</div>

<!-- 職務資訊 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">職務資訊</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 員工編號 -->
            <div>
                <label for="employee_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300">員工編號</label>
                <input type="text" name="employee_no" id="employee_no" value="{{ old('employee_no', $user->employee_no ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <!-- 職位 -->
            <div>
                <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">職位</label>
                <input type="text" name="position" id="position" value="{{ old('position', $user->position ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <!-- 角色層級 -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    角色層級 <span class="text-red-500">*</span>
                </label>
                <select name="role" id="role" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                    <option value="">請選擇角色</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $currentRole ?? '') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 上層主管 -->
            <div id="supervisor_field" style="display: {{ old('role', $currentRole ?? '') != 'Admin' ? 'block' : 'none' }};">
                <label for="supervisor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">上層主管</label>
                <select name="supervisor_id" id="supervisor_id"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                    <option value="">請選擇</option>
                    @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $user->supervisor_id ?? '') == $supervisor->id ? 'selected' : '' }}>
                            {{ $supervisor->name }}
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
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                    <option value="1" {{ old('is_active', $user->is_active ?? 1) ? 'selected' : '' }}>是</option>
                    <option value="0" {{ !old('is_active', $user->is_active ?? 1) ? 'selected' : '' }}>否</option>
                </select>
            </div>

            <!-- 到職日 -->
            <div>
                <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">到職日</label>
                <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $user->hire_date ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <!-- 離職日 -->
            <div>
                <label for="resign_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">離職日</label>
                <input type="date" name="resign_date" id="resign_date" value="{{ old('resign_date', $user->resign_date ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <!-- 停權日 -->
            <div>
                <label for="suspend_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">停權日</label>
                <input type="date" name="suspend_date" id="suspend_date" value="{{ old('suspend_date', $user->suspend_date ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <!-- 備註 -->
            <div class="md:col-span-2">
                <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">備註</label>
                <textarea name="note" id="note" rows="4"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">{{ old('note', $user->note ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<!-- 個人資訊 -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">個人資訊</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 身分證字號 -->
            <div>
                <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">身分證字號</label>
                <input type="text" name="id_number" id="id_number" value="{{ old('id_number', $user->id_number ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <!-- 出生年月日 -->
            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">出生年月日</label>
                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <!-- 電話 -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">電話 (市話)</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <!-- 手機 -->
            <div>
                <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">手機</label>
                <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $user->mobile ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <!-- 緊急聯絡人姓名 -->
            <div>
                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">緊急聯絡人姓名</label>
                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <!-- 緊急聯絡電話 -->
            <div>
                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">緊急聯絡電話</label>
                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone ?? '') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
            </div>
        </div>
    </div>
</div>
