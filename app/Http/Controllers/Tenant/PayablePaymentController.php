<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use App\Models\PayablePayment;
use Illuminate\Http\Request;

class PayablePaymentController extends Controller
{
    /**
     * 新增給付記錄（薪資入帳）
     */
    public function store(Request $request, Payable $payable)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'note' => 'nullable|string',
        ]);
        
        // 檢查金額是否超過剩餘應付
        $totalPaid = $payable->payments()->sum('amount');
        $remainingAmount = $payable->amount - $totalPaid;
        
        if ($validated['amount'] > $remainingAmount) {
            return back()->withErrors(['amount' => '給付金額不能超過剩餘應付金額'])->withInput();
        }
        
        // 建立給付記錄
        $payable->payments()->create($validated);
        
        // 更新應付帳款狀態和已付金額
        $newTotalPaid = $totalPaid + $validated['amount'];
        
        if ($newTotalPaid >= $payable->amount) {
            $payable->update([
                'status' => 'paid',
                'paid_amount' => $newTotalPaid,
            ]);
        } elseif ($newTotalPaid > 0) {
            $payable->update([
                'status' => 'partial',
                'paid_amount' => $newTotalPaid,
            ]);
        }
        
        return redirect()->route('tenant.payables.quick-pay', $payable)
            ->with('success', '給付記錄新增成功');
    }
    
    /**
     * 刪除給付記錄
     */
    public function destroy(PayablePayment $payment)
    {
        $payable = $payment->payable;
        $payment->delete();
        
        // 重新計算狀態和已付金額
        $totalPaid = $payable->payments()->sum('amount');
        
        if ($totalPaid >= $payable->amount) {
            $payable->update([
                'status' => 'paid',
                'paid_amount' => $totalPaid,
            ]);
        } elseif ($totalPaid > 0) {
            $payable->update([
                'status' => 'partial',
                'paid_amount' => $totalPaid,
            ]);
        } else {
            $payable->update([
                'status' => 'unpaid',
                'paid_amount' => 0,
            ]);
        }
        
        return redirect()->route('tenant.payables.quick-pay', $payable)
            ->with('success', '給付記錄刪除成功');
    }
}
