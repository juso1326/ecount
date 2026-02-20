@extends('layouts.tenant')

@section('title', '銀行帳戶管理')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">銀行帳戶管理</h1>
</div>

<!-- 新增銀行帳戶表單 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">新增銀行帳戶</h2>
    
    <form action="{{ route('tenant.settings.bank-accounts.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <span class="text-red-500">*</span> 銀行名稱
                </label>
                <input type="text" name="bank_name" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    分行
                </label>
                <input type="text" name="bank_branch"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <span class="text-red-500">*</span> 帳號
                </label>
                <input type="text" name="bank_account" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    戶名
                </label>
                <input type="text" name="account_name"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    備註
                </label>
                <textarea name="note" rows="2"
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2"></textarea>
            </div>
            
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">設為預設帳戶</span>
                </label>
            </div>
        </div>
        
        <div class="mt-4">
            <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg">
                新增
            </button>
        </div>
    </form>
</div>

<!-- 銀行帳戶列表 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">銀行名稱</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">分行</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">帳號</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">戶名</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">預設</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($bankAccounts as $account)
            <tr>
                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $account->bank_name }}
                </td>
                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $account->bank_branch ?? '-' }}
                </td>
                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $account->bank_account }}
                </td>
                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $account->account_name ?? '-' }}
                </td>
                <td class="px-6 py-3 whitespace-nowrap text-center text-sm">
                    @if($account->is_default)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            預設
                        </span>
                    @else
                        <form action="{{ route('tenant.settings.bank-accounts.set-default', $account) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-primary hover:text-primary-dark text-xs">
                                設為預設
                            </button>
                        </form>
                    @endif
                </td>
                <td class="px-6 py-3 whitespace-nowrap text-center text-sm">
                    <form action="{{ route('tenant.settings.bank-accounts.destroy', $account) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            刪除
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    尚無銀行帳戶資料
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
