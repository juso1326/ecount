@extends('layouts.tenant')

@section('title', '編輯使用者')

@section('content')
<form method="POST" action="{{ route('tenant.users.update', $user) }}">
    @csrf
    @method('PUT')
    
    @include('tenant.users._form')

    <div class="mt-6 flex justify-end space-x-3">
        <a href="{{ route('tenant.users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 font-bold py-2 px-6 rounded-lg">
            取消
        </a>
        <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-6 rounded-lg">
            更新
        </button>
    </div>
</form>

<!-- 銀行帳戶管理區塊 -->
<div class="mt-8">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">銀行帳戶管理</h3>
                <button type="button" onclick="openAddBankModal()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-plus mr-2"></i>新增銀行帳戶
                </button>
            </div>

            <div id="bankAccountsList">
                @if($user->bankAccounts->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->bankAccounts as $account)
                        <div class="flex items-center justify-between p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700" data-account-id="{{ $account->id }}">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $account->bank_name }}</span>
                                    @if($account->is_default)
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded dark:bg-blue-900 dark:text-blue-300">預設</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    @if($account->bank_branch)
                                        <span>{{ $account->bank_branch }}</span> •
                                    @endif
                                    <span>{{ $account->bank_account }}</span>
                                    @if($account->account_name)
                                        • <span>{{ $account->account_name }}</span>
                                    @endif
                                </div>
                                @if($account->note)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $account->note }}</div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                @if(!$account->is_default)
                                    <button type="button" onclick="setDefaultBank({{ $account->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <i class="fas fa-star"></i> 設為預設
                                    </button>
                                @endif
                                <button type="button" onclick="editBankAccount({{ $account->id }}, '{{ $account->bank_name }}', '{{ $account->bank_branch }}', '{{ $account->bank_account }}', '{{ $account->account_name }}', '{{ $account->note }}')" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" onclick="deleteBankAccount({{ $account->id }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-university text-4xl mb-2"></i>
                        <p>尚未新增銀行帳戶</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 新增/編輯銀行帳戶 Modal -->
<div id="bankAccountModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4" id="modalTitle">新增銀行帳戶</h3>
            <form id="bankAccountForm" onsubmit="saveBankAccount(event)">
                <input type="hidden" id="bankAccountId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">銀行名稱 <span class="text-red-500">*</span></label>
                    <input type="text" id="bank_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">分行名稱</label>
                    <input type="text" id="bank_branch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">帳號 <span class="text-red-500">*</span></label>
                    <input type="text" id="bank_account" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">戶名</label>
                    <input type="text" id="account_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">備註</label>
                    <textarea id="note" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="is_default" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">設為預設銀行帳戶</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeBankModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                        取消
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                        儲存
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const userId = {{ $user->id }};

// 開啟新增銀行帳戶 Modal
function openAddBankModal() {
    document.getElementById('modalTitle').textContent = '新增銀行帳戶';
    document.getElementById('bankAccountForm').reset();
    document.getElementById('bankAccountId').value = '';
    document.getElementById('bankAccountModal').classList.remove('hidden');
}

// 編輯銀行帳戶
function editBankAccount(id, bankName, bankBranch, bankAccount, accountName, note) {
    document.getElementById('modalTitle').textContent = '編輯銀行帳戶';
    document.getElementById('bankAccountId').value = id;
    document.getElementById('bank_name').value = bankName;
    document.getElementById('bank_branch').value = bankBranch || '';
    document.getElementById('bank_account').value = bankAccount;
    document.getElementById('account_name').value = accountName || '';
    document.getElementById('note').value = note || '';
    document.getElementById('bankAccountModal').classList.remove('hidden');
}

// 關閉 Modal
function closeBankModal() {
    document.getElementById('bankAccountModal').classList.add('hidden');
}

// 儲存銀行帳戶
async function saveBankAccount(event) {
    event.preventDefault();
    
    const accountId = document.getElementById('bankAccountId').value;
    const isEdit = accountId !== '';
    
    const data = {
        bank_name: document.getElementById('bank_name').value,
        bank_branch: document.getElementById('bank_branch').value,
        bank_account: document.getElementById('bank_account').value,
        account_name: document.getElementById('account_name').value,
        note: document.getElementById('note').value,
        is_default: document.getElementById('is_default').checked,
    };

    const url = isEdit 
        ? `/users/${userId}/bank-accounts/${accountId}`
        : `/users/${userId}/bank-accounts`;
    
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);
            location.reload();
        } else {
            alert('操作失敗: ' + (result.message || '未知錯誤'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('操作失敗，請稍後再試');
    }
}

// 刪除銀行帳戶
async function deleteBankAccount(id) {
    if (!confirm('確定要刪除此銀行帳戶嗎？')) {
        return;
    }

    try {
        const response = await fetch(`/users/${userId}/bank-accounts/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);
            location.reload();
        } else {
            alert('刪除失敗: ' + (result.message || '未知錯誤'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('刪除失敗，請稍後再試');
    }
}

// 設為預設銀行帳戶
async function setDefaultBank(id) {
    try {
        const response = await fetch(`/users/${userId}/bank-accounts/${id}/set-default`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);
            location.reload();
        } else {
            alert('設定失敗: ' + (result.message || '未知錯誤'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('設定失敗，請稍後再試');
    }
}

// 根據角色顯示/隱藏上層主管欄位
document.getElementById('role').addEventListener('change', function() {
    const supervisorField = document.getElementById('supervisor_field');
    if (this.value && this.value !== 'Admin') {
        supervisorField.style.display = 'block';
    } else {
        supervisorField.style.display = 'none';
    }
});
</script>

@endsection
