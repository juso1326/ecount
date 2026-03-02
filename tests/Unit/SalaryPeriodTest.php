<?php

namespace Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Tests for salary closing day / default period logic.
 * Mirrors the logic in SalaryController::defaultSalaryPeriod()
 * and SalaryService::getSalaryPeriod()
 */
class SalaryPeriodTest extends TestCase
{
    /**
     * Replicate defaultSalaryPeriod() logic for testing.
     * If today < closingDay → show previous month, else current month.
     */
    private function defaultSalaryPeriod(Carbon $today, int $closingDay): array
    {
        if ($today->day < $closingDay) {
            $ref = $today->copy()->subMonth();
        } else {
            $ref = $today->copy();
        }
        return [(string) $ref->year, $ref->format('m')];
    }

    /**
     * Replicate getSalaryPeriod() label generation.
     */
    private function getSalaryPeriodLabel(int $year, int $month, int $closingDay): string
    {
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $actualClosingDay = min($closingDay, $daysInMonth);
        $startDate = Carbon::create($year, $month, $actualClosingDay)->startOfDay();
        $endDate = $startDate->copy()->addMonth()->subDay()->endOfDay();
        return $startDate->format('Y/m/d') . ' ~ ' . $endDate->format('Y/m/d');
    }

    public function test_before_closing_day_shows_previous_month(): void
    {
        // Today is 2026-03-01, closing day is 3 → day(1) < 3 → show February
        $today = Carbon::create(2026, 3, 1);
        [$year, $month] = $this->defaultSalaryPeriod($today, 3);
        $this->assertSame('2026', $year);
        $this->assertSame('02', $month);
    }

    public function test_on_closing_day_shows_current_month(): void
    {
        // Today is 2026-03-03, closing day is 3 → day(3) == 3, not less → show March
        $today = Carbon::create(2026, 3, 3);
        [$year, $month] = $this->defaultSalaryPeriod($today, 3);
        $this->assertSame('2026', $year);
        $this->assertSame('03', $month);
    }

    public function test_after_closing_day_shows_current_month(): void
    {
        // Today is 2026-03-10, closing day is 3 → day(10) > 3 → show March
        $today = Carbon::create(2026, 3, 10);
        [$year, $month] = $this->defaultSalaryPeriod($today, 3);
        $this->assertSame('2026', $year);
        $this->assertSame('03', $month);
    }

    public function test_year_rollover_january_before_closing(): void
    {
        // Today is 2026-01-02, closing day is 5 → day(2) < 5 → show December 2025
        $today = Carbon::create(2026, 1, 2);
        [$year, $month] = $this->defaultSalaryPeriod($today, 5);
        $this->assertSame('2025', $year);
        $this->assertSame('12', $month);
    }

    public function test_period_label_march_closing_day_3(): void
    {
        // Period for 2026/02 with closing day 3:
        // start: 2026/02/03, end: 2026/03/02
        $label = $this->getSalaryPeriodLabel(2026, 2, 3);
        $this->assertSame('2026/02/03 ~ 2026/03/02', $label);
    }

    public function test_period_label_closing_day_clamps_to_month_end(): void
    {
        // February has 28 days in 2026. Closing day 31 → clamps to 28.
        $label = $this->getSalaryPeriodLabel(2026, 2, 31);
        $this->assertSame('2026/02/28 ~ 2026/03/27', $label);
    }

    public function test_period_label_closing_day_1(): void
    {
        // start: 2026/03/01, end: 2026/03/31
        $label = $this->getSalaryPeriodLabel(2026, 3, 1);
        $this->assertSame('2026/03/01 ~ 2026/03/31', $label);
    }
}
