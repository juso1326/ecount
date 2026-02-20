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
        
        // 更新應收帳款狀態和已收金額
        $newTotalReceived = $totalReceived + $validated['amount'];
        
        if ($newTotalReceived >= $receivable->amount) {
            $receivable->update([
                'status' => 'paid',
                'received_amount' => $newTotalReceived,
            ]);
        } elseif ($newTotalReceived > 0) {
            $receivable->update([
                'status' => 'partial',
                'received_amount' => $newTotalReceived,
            ]);
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
        
        // 重新計算狀態和已收金額
        $totalReceived = $receivable->payments()->sum('amount');
        
        if ($totalReceived >= $receivable->amount) {
            $receivable->update([
                'status' => 'paid',
                'received_amount' => $totalReceived,
            ]);
        } elseif ($totalReceived > 0) {
            $receivable->update([
                'status' => 'partial',
                'received_amount' => $totalReceived,
            ]);
        } else {
            $receivable->update([
                'status' => 'unpaid',
                'received_amount' => 0,
            ]);
        }
        
        return redirect()->route('tenant.receivables.quick-receive', $receivable)
            ->with('success', '入帳記錄刪除成功');
    }
}
