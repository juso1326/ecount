<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SalaryAdjustment;
use App\Services\SalaryService;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    protected $salaryService;

    public function __construct(SalaryService $salaryService)
    {
        $this->salaryService = $salaryService;
    }

    /**
     * 薪資總表
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $data = $this->salaryService->getMonthlySalaries($year, $month);

        return view('tenant.salaries.index', [
            'salaries' => $data['salaries'],
            'period' => $data['period'],
            'total' => $data['total'],
            'year' => $year,
            'month' => $month,
        ]);
    }

    /**
     * 個人薪資明細
     */
    public function show(Request $request, User $user)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $salary = $this->salaryService->calculateMonthlySalary($user->id, $year, $month);
        $isPaid = $this->salaryService->isPaid($user->id, $year, $month);

        return view('tenant.salaries.show', [
            'user' => $user,
            'salary' => $salary,
            'isPaid' => $isPaid,
            'year' => $year,
            'month' => $month,
        ]);
    }

    /**
     * 加扣項管理
     */
    public function adjustments(User $user)
    {
        $adjustments = SalaryAdjustment::where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('tenant.salaries.adjustments', [
            'user' => $user,
            'adjustments' => $adjustments,
        ]);
    }

    /**
     * 新增加扣項
     */
    public function storeAdjustment(Request $request, User $user)
    {
        $validated = $request->validate([
            'type' => 'required|in:add,deduct',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'recurrence' => 'required|in:once,monthly,fixed',
            'remark' => 'nullable|string',
        ]);

        $validated['user_id'] = $user->id;

        SalaryAdjustment::create($validated);

        return redirect()->route('tenant.salaries.adjustments', $user)
            ->with('success', '加扣項新增成功');
    }

    /**
     * 刪除加扣項
     */
    public function destroyAdjustment(SalaryAdjustment $adjustment)
    {
        $userId = $adjustment->user_id;
        $adjustment->delete();

        return redirect()->route('tenant.salaries.adjustments', $userId)
            ->with('success', '加扣項刪除成功');
    }

    /**
     * 薪資撥款
     */
    public function pay(Request $request, User $user)
    {
        $validated = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
            'actual_amount' => 'required|numeric|min:0',
            'remark' => 'nullable|string',
        ]);

        $this->salaryService->markAsPaid(
            $user->id,
            $validated['year'],
            $validated['month'],
            $validated['actual_amount'],
            $validated['remark'] ?? null
        );

        return redirect()->route('tenant.salaries.show', [
            'user' => $user,
            'year' => $validated['year'],
            'month' => $validated['month']
        ])->with('success', '薪資撥款成功');
    }
}

