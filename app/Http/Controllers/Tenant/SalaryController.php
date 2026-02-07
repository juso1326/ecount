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
        
        // 處理月份導航
        if ($request->get('nav') === 'prev') {
            $date = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
            $year = $date->year;
            $month = $date->format('m');
        } elseif ($request->get('nav') === 'next') {
            $date = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
            $year = $date->year;
            $month = $date->format('m');
        }
        
        // 限制最早月份為 2014年9月
        if ($year < 2014 || ($year == 2014 && $month < 9)) {
            $year = 2014;
            $month = '09';
        }

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
        
        // 處理月份導航
        if ($request->get('nav') === 'prev') {
            $date = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
            $year = $date->year;
            $month = $date->format('m');
        } elseif ($request->get('nav') === 'next') {
            $date = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
            $year = $date->year;
            $month = $date->format('m');
        }
        
        // 限制最早月份為 2014年9月
        if ($year < 2014 || ($year == 2014 && $month < 9)) {
            $year = 2014;
            $month = '09';
        }

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
    
    /**
     * 廠商支付列表
     */
    public function vendors(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        // 處理月份導航
        if ($request->get('nav') === 'prev') {
            $date = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
            $year = $date->year;
            $month = $date->format('m');
        } elseif ($request->get('nav') === 'next') {
            $date = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
            $year = $date->year;
            $month = $date->format('m');
        }
        
        // 限制最早月份為 2014年9月
        if ($year < 2014 || ($year == 2014 && $month < 9)) {
            $year = 2014;
            $month = '09';
        }

        $data = $this->salaryService->getVendorPayments($year, $month);

        return view('tenant.salaries.vendors', [
            'payments' => $data['payments'],
            'period' => $data['period'],
            'total' => $data['total'],
            'paid_total' => $data['paid_total'],
            'unpaid_total' => $data['unpaid_total'],
            'year' => $year,
            'month' => $month,
        ]);
    }
    
    /**
     * 移動薪資項目到上個月
     */
    public function moveToPrevMonth(Request $request)
    {
        try {
            $this->salaryService->moveToPreviousMonth($request->payable_id);
            return response()->json(['success' => true, 'message' => '已移到上個月']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * 移動薪資項目到下個月
     */
    public function moveToNextMonth(Request $request)
    {
        try {
            $this->salaryService->moveToNextMonth($request->payable_id);
            return response()->json(['success' => true, 'message' => '已移到下個月']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}

