<?php

namespace App\Services;

use App\Models\Payable;
use App\Models\SalaryAdjustment;
use App\Models\TenantSetting;
use Carbon\Carbon;

class SalaryService
{
    /**
     * 計算員工月薪資
     */
    public function calculateMonthlySalary($userId, $year, $month)
    {
        $period = $this->getSalaryPeriod($year, $month);
        
        // 1. 基本薪資（應付帳款中的員工薪資）
        $baseSalary = Payable::where('payee_user_id', $userId)
            ->where('payee_type', 'user')
            ->whereBetween('payment_date', [$period['start'], $period['end']])
            ->sum('amount');
        
        // 2. 加扣項
        $adjustments = $this->calculateAdjustments($userId, $period['start'], $period['end']);
        
        // 3. 總計
        $total = $baseSalary + $adjustments['total'];
        
        return [
            'base_salary' => $baseSalary,
            'additions' => $adjustments['additions'],
            'deductions' => $adjustments['deductions'],
            'adjustments_total' => $adjustments['total'],
            'total' => $total,
            'period' => $period,
            'items' => $this->getSalaryItems($userId, $period['start'], $period['end']),
        ];
    }
    
    /**
     * 取得薪資週期
     */
    public function getSalaryPeriod($year, $month)
    {
        $closingDay = TenantSetting::get('closing_day', 1);
        
        // 確保關帳日不超過該月天數
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $actualClosingDay = min($closingDay, $daysInMonth);
        
        $startDate = Carbon::create($year, $month, $actualClosingDay)->startOfDay();
        $endDate = $startDate->copy()->addMonth()->subDay()->endOfDay();
        
        return [
            'start' => $startDate,
            'end' => $endDate,
            'label' => $startDate->format('Y/m/d') . ' ~ ' . $endDate->format('Y/m/d'),
            'closing_day' => $closingDay,
            'year' => $year,
            'month' => $month,
        ];
    }
    
    /**
     * 計算加扣項
     */
    protected function calculateAdjustments($userId, $startDate, $endDate)
    {
        $adjustments = SalaryAdjustment::where('user_id', $userId)
            ->active()
            ->inPeriod($startDate, $endDate)
            ->get();
        
        $additions = 0;
        $deductions = 0;
        
        foreach ($adjustments as $adj) {
            if ($adj->isAddition()) {
                $additions += $adj->amount;
            } else {
                $deductions += $adj->amount;
            }
        }
        
        return [
            'additions' => $additions,
            'deductions' => $deductions,
            'total' => $additions - $deductions,
            'items' => $adjustments,
        ];
    }
    
    /**
     * 取得薪資明細項目
     */
    protected function getSalaryItems($userId, $startDate, $endDate)
    {
        return Payable::where('payee_user_id', $userId)
            ->where('payee_type', 'user')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with(['project', 'responsibleUser'])
            ->orderBy('payment_date')
            ->get();
    }
    
    /**
     * 標記為已撥款
     */
    public function markAsPaid($userId, $year, $month, $actualAmount, $remark = null)
    {
        $period = $this->getSalaryPeriod($year, $month);
        
        $payables = Payable::where('payee_user_id', $userId)
            ->where('payee_type', 'user')
            ->whereBetween('payment_date', [$period['start'], $period['end']])
            ->get();
        
        foreach ($payables as $payable) {
            $payable->update([
                'is_salary_paid' => true,
                'salary_paid_at' => now(),
                'salary_paid_amount' => $actualAmount,
                'salary_paid_remark' => $remark,
            ]);
        }
        
        return true;
    }
    
    /**
     * 取得所有員工的薪資總表
     */
    public function getMonthlySalaries($year, $month)
    {
        $period = $this->getSalaryPeriod($year, $month);
        
        // 取得在此期間有薪資的所有員工
        $userIds = Payable::where('payee_type', 'user')
            ->whereBetween('payment_date', [$period['start'], $period['end']])
            ->distinct()
            ->pluck('payee_user_id');
        
        $salaries = [];
        foreach ($userIds as $userId) {
            $salary = $this->calculateMonthlySalary($userId, $year, $month);
            $salary['user'] = \App\Models\User::find($userId);
            $salaries[] = $salary;
        }
        
        return [
            'salaries' => $salaries,
            'period' => $period,
            'total' => collect($salaries)->sum('total'),
        ];
    }
    
    /**
     * 檢查是否已撥款
     */
    public function isPaid($userId, $year, $month)
    {
        $period = $this->getSalaryPeriod($year, $month);
        
        $unpaidCount = Payable::where('payee_user_id', $userId)
            ->where('payee_type', 'user')
            ->whereBetween('payment_date', [$period['start'], $period['end']])
            ->where('is_salary_paid', false)
            ->count();
        
        return $unpaidCount === 0;
    }
    
    /**
     * 取得廠商支付記錄
     */
    public function getVendorPayments($year, $month)
    {
        $period = $this->getSalaryPeriod($year, $month);
        
        $payments = Payable::with(['company', 'project'])
            ->where('payee_type', 'company')
            ->whereBetween('payment_date', [$period['start'], $period['end']])
            ->whereNotNull('payee_company_id')
            ->orderBy('payment_date')
            ->orderBy('company_id')
            ->get();
        
        $groupedPayments = $payments->groupBy('payee_company_id')->map(function ($items, $companyId) {
            $company = $items->first()->payeeCompany;
            return [
                'company' => $company,
                'items' => $items,
                'total' => $items->sum('amount'),
                'paid_total' => $items->where('is_salary_paid', true)->sum('salary_paid_amount'),
                'unpaid_total' => $items->where('is_salary_paid', false)->sum('amount'),
            ];
        });
        
        return [
            'payments' => $groupedPayments,
            'period' => $period,
            'total' => $payments->sum('amount'),
            'paid_total' => $payments->where('is_salary_paid', true)->sum('salary_paid_amount'),
            'unpaid_total' => $payments->where('is_salary_paid', false)->sum('amount'),
        ];
    }
    
    /**
     * 移動薪資項目到上個月
     */
    public function moveToPreviousMonth($payableId)
    {
        $payable = Payable::findOrFail($payableId);
        
        // 檢查是否已撥款
        if ($payable->is_salary_paid) {
            throw new \Exception('已撥款的項目無法移動');
        }
        
        // 計算上個月的日期（移到上個月的6號）
        $currentDate = Carbon::parse($payable->payment_date);
        $newDate = $currentDate->subMonth()->setDay(6);
        
        // 更新日期
        $payable->payment_date = $newDate;
        $payable->save();
        
        return $payable;
    }
    
    /**
     * 移動薪資項目到下個月
     */
    public function moveToNextMonth($payableId)
    {
        $payable = Payable::findOrFail($payableId);
        
        // 檢查是否已撥款
        if ($payable->is_salary_paid) {
            throw new \Exception('已撥款的項目無法移動');
        }
        
        // 計算下個月的日期（移到下個月的6號）
        $currentDate = Carbon::parse($payable->payment_date);
        $newDate = $currentDate->addMonth()->setDay(6);
        
        // 更新日期
        $payable->payment_date = $newDate;
        $payable->save();
        
        return $payable;
    }
}
