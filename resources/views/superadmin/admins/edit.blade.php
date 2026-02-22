@extends('layouts.superadmin')

@section('title', '編輯帳號')
@section('page-title', '編輯帳號')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('superadmin.admins.update', $admin) }}" method="POST"
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">名稱 *</label>
            <input type="text" name="name" value="{{ old('name', $admin->name) }}" required
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-white text-sm">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email *</label>
            <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-white text-sm">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">留空則不變更密碼</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">新密碼</label>
                    <input type="password" name="password" autocomplete="new-password"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-white text-sm">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">確認新密碼</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-white text-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('superadmin.admins.index') }}"
               class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
                取消
            </a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 text-sm">
                儲存變更
            </button>
        </div>
    </form>
</div>
@endsection
