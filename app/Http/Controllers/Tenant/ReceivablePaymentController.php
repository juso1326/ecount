<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use Illuminate\Http\Request;

class ReceivablePaymentController extends Controller
{
    /**
     * 新增入帳記錄
     */
    public function store(Request $request, Receivable $receivable)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'note' => 'nullable|string',
        ]);
        
        // 檢查金額是否超過剩餘應收
        $totalReceived = $receivable->payments()->sum('amount');
        $remainingAmount = $receivable->amount - $totalReceived;
        
        if ($validated['amount'] > $remainingAmount) {
            return back()->withErrors(['amount' => '入帳金額不能超過剩餘應收金額'])->withInput();
        }
        
        // 建立入帳記錄
        $receivable->payments()->create($validated);
        
        // 更新應收帳款狀態
        $newTotalReceived = $totalReceived + $validated['amount'];
        
        if ($newTotalReceived >= $receivable->amount) {
            $receivable->update(['status' => 'paid']);
        } elseif ($newTotalReceived > 0) {
            $receivable->update(['status' => 'partial']);
        }
        
        return redirect()->route('tenant.receivables.quick-receive', $receivable)
            ->with('success', '入帳記錄新增成功');
    }
    
    /**
     * 刪除入帳記錄
     */
    public function destroy(ReceivablePayment $payment)
    {
        $receivable = $payment->receivable;
        $payment->delete();
        
        // 重新計算狀態
        $totalReceived = $receivable->payments()->sum('amount');
        
        if ($totalReceived >= $receivable->amount) {
            $receivable->update(['status' => 'paid']);
        } elseif ($totalReceived > 0) {
            $receivable->update(['status' => 'partial']);
        } else {
            $receivable->update(['status' => 'unpaid']);
        }
        
        return redirect()->route('tenant.receivables.quick-receive', $receivable)
            ->with('success', '入帳記錄刪除成功');
    }
}
