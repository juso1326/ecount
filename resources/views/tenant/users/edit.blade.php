@extends('layouts.tenant')

@section('title', '編輯使用者')

@section('content')
<!-- Breadcrumb -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black dark:text-white">
        編輯使用者
    </h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li>
                <a class="font-medium" href="{{ route('tenant.dashboard') }}">首頁 /</a>
            </li>
            <li>
                <a class="font-medium" href="{{ route('tenant.users.index') }}">使用者管理 /</a>
            </li>
            <li class="font-medium text-primary">編輯</li>
        </ol>
    </nav>
</div>

<div class="grid grid-cols-1 gap-9">
    <div class="flex flex-col gap-9">
        <!-- 表單 -->
        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <form method="POST" action="{{ route('tenant.users.update', $user) }}">
                @csrf
                @method('PUT')
                
                <!-- 1. 角色與權限 -->
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        角色與權限
                    </h3>
                </div>
                <div class="p-6.5">
                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                角色層級 <span class="text-meta-1">*</span>
                            </label>
                            <select name="role" id="role" required
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                                <option value="">請選擇角色</option>
                                <option value="admin" {{ old('role', $currentRole) == 'admin' ? 'selected' : '' }}>系統管理員</option>
                                <option value="manager" {{ old('role', $currentRole) == 'manager' ? 'selected' : '' }}>總管理/主管</option>
                                <option value="accountant" {{ old('role', $currentRole) == 'accountant' ? 'selected' : '' }}>會計</option>
                                <option value="employee" {{ old('role', $currentRole) == 'employee' ? 'selected' : '' }}>成員</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full xl:w-1/2" id="supervisor_field" style="display: {{ old('role', $currentRole) != 'admin' ? 'block' : 'none' }};">
                            <label class="mb-2.5 block text-black dark:text-white">
                                上層主管
                            </label>
                            <select name="supervisor_id" id="supervisor_id"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
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

                <!-- 2. 基本資訊 -->
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        基本資訊
                    </h3>
                </div>
                <div class="p-6.5">
                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                員工編號
                            </label>
                            <input type="text" name="employee_no" id="employee_no" value="{{ old('employee_no', $user->employee_no) }}"
                                placeholder="請輸入員工編號"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>

                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                姓名 <span class="text-meta-1">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                placeholder="請輸入姓名"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                            @error('name')
                                <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                簡稱
                            </label>
                            <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $user->short_name) }}"
                                placeholder="請輸入簡稱"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>

                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                職位
                            </label>
                            <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}"
                                placeholder="請輸入職位"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>
                    </div>

                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                是否在職 <span class="text-meta-1">*</span>
                            </label>
                            <select name="is_active" id="is_active" required
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                                <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>是</option>
                                <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>否</option>
                            </select>
                        </div>

                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                部門
                            </label>
                            <select name="department_id" id="department_id"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
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

                <!-- 3. 帳號資訊 -->
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        帳號資訊
                    </h3>
                </div>
                <div class="p-6.5">
                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                登入帳號 (Email) <span class="text-meta-1">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                placeholder="請輸入 Email"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                            @error('email')
                                <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                密碼 <span class="text-xs text-bodydark">(留空表示不變更)</span>
                            </label>
                            <input type="password" name="password" id="password"
                                placeholder="請輸入新密碼（至少 6 字元）"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                            @error('password')
                                <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            備份 Email
                        </label>
                        <input type="email" name="backup_email" id="backup_email" value="{{ old('backup_email', $user->backup_email) }}"
                            placeholder="請輸入備份 Email"
                            class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                    </div>
                </div>

                <!-- 4. 個人資料 -->
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        個人資料
                    </h3>
                </div>
                <div class="p-6.5">
                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                身分證字號
                            </label>
                            <input type="text" name="id_number" id="id_number" value="{{ old('id_number', $user->id_number) }}"
                                placeholder="請輸入身分證字號"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>

                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                出生年月日
                            </label>
                            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>
                    </div>

                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                電話 (市話)
                            </label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                placeholder="請輸入電話"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>

                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                手機
                            </label>
                            <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $user->mobile) }}"
                                placeholder="請輸入手機"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>
                    </div>
                </div>

                <!-- 5. 銀行資訊 -->
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        銀行帳戶資訊
                    </h3>
                </div>
                <div class="p-6.5">
                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <div class="w-full xl:w-1/3">
                            <label class="mb-2.5 block text-black dark:text-white">
                                銀行
                            </label>
                            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $user->bank_name) }}"
                                placeholder="請輸入銀行名稱"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>

                        <div class="w-full xl:w-1/3">
                            <label class="mb-2.5 block text-black dark:text-white">
                                分行
                            </label>
                            <input type="text" name="bank_branch" id="bank_branch" value="{{ old('bank_branch', $user->bank_branch) }}"
                                placeholder="請輸入分行名稱"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>

                        <div class="w-full xl:w-1/3">
                            <label class="mb-2.5 block text-black dark:text-white">
                                帳號
                            </label>
                            <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account', $user->bank_account) }}"
                                placeholder="請輸入銀行帳號"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>
                    </div>
                </div>

                <!-- 6. 緊急聯絡人 -->
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        緊急聯絡人
                    </h3>
                </div>
                <div class="p-6.5">
                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                姓名
                            </label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}"
                                placeholder="請輸入緊急聯絡人姓名"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>

                        <div class="w-full xl:w-1/2">
                            <label class="mb-2.5 block text-black dark:text-white">
                                聯絡電話
                            </label>
                            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}"
                                placeholder="請輸入緊急聯絡電話"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>
                    </div>
                </div>

                <!-- 7. 任職資訊 -->
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        任職資訊
                    </h3>
                </div>
                <div class="p-6.5">
                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <div class="w-full xl:w-1/3">
                            <label class="mb-2.5 block text-black dark:text-white">
                                到職日
                            </label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $user->hire_date?->format('Y-m-d')) }}"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>

                        <div class="w-full xl:w-1/3">
                            <label class="mb-2.5 block text-black dark:text-white">
                                離職日
                            </label>
                            <input type="date" name="resign_date" id="resign_date" value="{{ old('resign_date', $user->resign_date?->format('Y-m-d')) }}"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>

                        <div class="w-full xl:w-1/3">
                            <label class="mb-2.5 block text-black dark:text-white">
                                停權日
                            </label>
                            <input type="date" name="suspend_date" id="suspend_date" value="{{ old('suspend_date', $user->suspend_date?->format('Y-m-d')) }}"
                                class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        </div>
                    </div>
                </div>

                <!-- 8. 備註 -->
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        備註
                    </h3>
                </div>
                <div class="p-6.5">
                    <div class="mb-6">
                        <label class="mb-2.5 block text-black dark:text-white">
                            備註
                        </label>
                        <textarea name="note" id="note" rows="4"
                            placeholder="請輸入備註內容"
                            class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">{{ old('note', $user->note) }}</textarea>
                    </div>

                    <!-- 按鈕 -->
                    <div class="flex justify-end gap-4.5">
                        <a href="{{ route('tenant.users.index') }}" 
                           class="flex justify-center rounded border border-stroke py-2 px-6 font-medium text-black hover:shadow-1 dark:border-strokedark dark:text-white">
                            取消
                        </a>
                        <button type="submit" 
                                class="flex justify-center rounded bg-primary py-2 px-6 font-medium text-gray hover:bg-opacity-90">
                            更新
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
