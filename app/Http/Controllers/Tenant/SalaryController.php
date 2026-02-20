<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SalaryAdjustment;
use App\Services\SalaryService;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        // 動態產生年份選單：從租戶建立年份到明年
        $tenant = auth()->user()->tenant ?? tenancy()->tenant;
        $tenantCreatedYear = $tenant && $tenant->created_at ? $tenant->created_at->year : 2014;
        $startYear = $tenantCreatedYear;
        $endYear = date('Y') + 1;

        return view('tenant.salaries.index', [
            'salaries' => $data['salaries'],
            'period' => $data['period'],
            'total' => $data['total'],
            'year' => $year,
            'month' => $month,
            'startYear' => $startYear,
            'endYear' => $endYear,
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
        
        // 取得加扣項明細，按週期分組
        $adjustmentsDetail = $salary['adjustments_items'] ?? collect();
        $periodicAdjustments = $adjustmentsDetail->whereIn('recurrence', ['monthly', 'yearly']);
        $onceAdjustments = $adjustmentsDetail->where('recurrence', 'once');

        return view('tenant.salaries.show', [
            'user' => $user,
            'salary' => $salary,
            'isPaid' => $isPaid,
            'year' => $year,
            'month' => $month,
            'currentYear' => $year,
            'currentMonth' => $month,
            'periodicAdjustments' => $periodicAdjustments,
            'onceAdjustments' => $onceAdjustments,
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

        // 將空字串的日期欄位轉為 null
        if (isset($validated['end_date']) && $validated['end_date'] === '') {
            $validated['end_date'] = null;
        }

        $validated['user_id'] = $user->id;

        SalaryAdjustment::create($validated);

        return redirect()->route('tenant.salaries.adjustments', $user)
            ->with('success', '加扣項新增成功');
    }

    /**
     * 快速新增加扣項（薪資頁面專用）
     */
    public function storeQuickAdjustment(Request $request, User $user)
    {
        $validated = $request->validate([
            'type' => 'required|in:add,deduct',
            'title' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'recurrence' => 'required|in:once,monthly,yearly',
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
            'remark' => 'nullable|string|max:500',
        ]);
        
        // 檢查該月是否已撥款
        if ($this->salaryService->isPaid($user->id, $validated['year'], $validated['month'])) {
            return response()->json([
                'success' => false,
                'message' => '該月份薪資已撥款，無法新增加扣項'
            ], 403);
        }
        
        // 自動計算日期
        $startDate = Carbon::create($validated['year'], $validated['month'], 1);
        $endDate = $validated['recurrence'] === 'once' 
            ? $startDate->copy()->endOfMonth() 
            : null;
        
        $adjustment = SalaryAdjustment::create([
            'user_id' => $user->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'recurrence' => $validated['recurrence'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => true,
            'remark' => $validated['remark'] ?? null,
        ]);
        
        // 重新計算薪資
        $salary = $this->salaryService->calculateMonthlySalary(
            $user->id, 
            $validated['year'], 
            $validated['month']
        );
        
        return response()->json([
            'success' => true,
            'message' => '加扣項新增成功',
            'adjustment' => $adjustment,
            'new_totals' => [
                'base_salary' => $salary['base_salary'],
                'additions' => $salary['additions'],
                'deductions' => $salary['deductions'],
                'total' => $salary['total'],
            ]
        ]);
    }
    
    /**
     * 更新加扣項
     */
    public function updateAdjustment(Request $request, SalaryAdjustment $adjustment)
    {
        // 只允許編輯單次加扣項
        if ($adjustment->recurrence !== 'once') {
            return response()->json([
                'success' => false,
                'message' => '週期性加扣項請至加扣項管理頁面編輯'
            ], 403);
        }
        
        // 檢查是否已撥款
        $period = Carbon::parse($adjustment->start_date);
        if ($this->salaryService->isPaid($adjustment->user_id, $period->year, $period->month)) {
            return response()->json([
                'success' => false,
                'message' => '該月份薪資已撥款，無法編輯'
            ], 403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'remark' => 'nullable|string|max:500',
        ]);
        
        $adjustment->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => '加扣項更新成功',
            'adjustment' => $adjustment
        ]);
    }

    /**
     * 刪除加扣項
     */
    public function destroyAdjustment(SalaryAdjustment $adjustment)
    {
        // 如果是 AJAX 請求，返回 JSON
        if (request()->wantsJson()) {
            $adjustment->delete();
            return response()->json([
                'success' => true,
                'message' => '加扣項刪除成功'
            ]);
        }
        
        // 原有的重定向邏輯
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

    /**
     * 排除週期性加扣項（本月停用）
     */
    public function excludeAdjustment(Request $request, User $user, SalaryAdjustment $adjustment)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        \App\Models\SalaryAdjustmentExclusion::firstOrCreate([
            'salary_adjustment_id' => $adjustment->id,
            'year' => $year,
            'month' => $month,
        ]);

        return redirect()->back()->with('success', '已停用本月加扣項');
    }

    /**
     * 恢復週期性加扣項
     */
    public function restoreAdjustment(Request $request, User $user, SalaryAdjustment $adjustment)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        \App\Models\SalaryAdjustmentExclusion::where([
            'salary_adjustment_id' => $adjustment->id,
            'year' => $year,
            'month' => $month,
        ])->delete();

        return redirect()->back()->with('success', '已恢復本月加扣項');
    }
}

