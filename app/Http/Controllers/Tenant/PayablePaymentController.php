<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use App\Models\PayablePayment;
use Illuminate\Http\Request;

class PayablePaymentController extends Controller
{
    /**
     * 取得出帳記錄（JSON API）
     */
    public function getPayments(Payable $payable)
    {
        $payments = $payable->payments()
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get(['id', 'payment_date', 'payment_method', 'amount', 'note']);

        return response()->json($payments);
    }

    /**
     * 新增出帳記錄
     */
    public function store(Request $request, Payable $payable)
    {
        $validated = $request->validate([
            'payment_date'   => 'required|date',
            'amount'         => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'note'           => 'nullable|string',
        ]);

        if (isset($validated['payment_date']) && $validated['payment_date'] === '') {
            $validated['payment_date'] = null;
        }

        $totalPaid = $payable->payments()->sum('amount');
        $remainingAmount = $payable->amount - $totalPaid;

        if ($validated['amount'] > $remainingAmount + 0.001) {
            return back()->withErrors(['amount' => '給付金額不能超過剩餘應付金額'])->withInput();
        }

        $payable->payments()->create($validated);

        $newTotalPaid = $payable->payments()->sum('amount');
        $latestDate   = $payable->payments()->orderByDesc('payment_date')->value('payment_date');

        $payable->update([
            'paid_amount' => $newTotalPaid,
            'paid_date'   => $latestDate,
            'status'      => $newTotalPaid >= $payable->amount ? 'paid'
                           : ($newTotalPaid > 0 ? 'partial' : 'unpaid'),
        ]);

        return redirect()->route('tenant.payables.index')->with('success', '出帳記錄新增成功');
    }

    /**
     * 刪除出帳記錄
     */
    public function destroy(PayablePayment $payment)
    {
        $payable = $payment->payable;
        $payment->delete();

        $totalPaid  = $payable->payments()->sum('amount');
        $latestDate = $payable->payments()->orderByDesc('payment_date')->value('payment_date');

        $payable->update([
            'paid_amount' => $totalPaid,
            'paid_date'   => $latestDate,
            'status'      => $totalPaid >= $payable->amount ? 'paid'
                           : ($totalPaid > 0 ? 'partial' : 'unpaid'),
        ]);

        return redirect()->route('tenant.payables.index')->with('success', '出帳記錄已刪除');
    }
}
