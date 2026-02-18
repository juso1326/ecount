<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('tenant.settings.bank-accounts', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'bank_account' => 'required|string|max:50',
            'account_name' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'note' => 'nullable|string',
        ]);

        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = true;

        BankAccount::create($validated);

        return back()->with('success', '銀行帳戶新增成功');
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'bank_account' => 'required|string|max:50',
            'account_name' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'note' => 'nullable|string',
        ]);

        $validated['is_default'] = $request->boolean('is_default');

        $bankAccount->update($validated);

        return back()->with('success', '銀行帳戶更新成功');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
        return back()->with('success', '銀行帳戶刪除成功');
    }

    public function setDefault(BankAccount $bankAccount)
    {
        $bankAccount->update(['is_default' => true]);
        return back()->with('success', '已設定為預設帳戶');
    }
}
