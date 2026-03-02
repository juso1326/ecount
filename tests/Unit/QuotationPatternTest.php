<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests for quotation number pattern generation logic.
 * Mirrors the JS updateQtnExample() and PHP quotationExample logic in SettingsController.
 */
class QuotationPatternTest extends TestCase
{
    /**
     * Replicate SettingsController quotationExample generation.
     * Order: replace A+ → prefix, then 0+ → padded number, then YYYY/YY → year.
     */
    private function generateExample(string $pattern, int $year = 2026): string
    {
        preg_match('/0+/', $pattern, $zeroMatches);
        $zeros = $zeroMatches[0] ?? '0000';
        $padded = str_pad('1', strlen($zeros), '0', STR_PAD_LEFT);

        $example = preg_replace('/A+/', 'REB', $pattern);
        $example = preg_replace('/0+/', $padded, $example);
        $example = preg_replace('/Y{4}/', (string) $year, $example);
        $example = preg_replace('/Y+/', substr((string) $year, -2), $example);
        return $example;
    }

    private function isValidPattern(string $pattern): bool
    {
        return (bool) preg_match('/0+/', $pattern);
    }

    public function test_default_pattern_generates_example(): void
    {
        $example = $this->generateExample('AAAYYYY0000', 2026);
        $this->assertSame('REB20260001', $example);
    }

    public function test_custom_prefix_pattern(): void
    {
        $example = $this->generateExample('INVYYYY000', 2026);
        $this->assertSame('INV2026001', $example);
    }

    public function test_pattern_with_two_year_digits(): void
    {
        // YY = last 2 digits of year
        $example = $this->generateExample('REBYY0000', 2026);
        $this->assertSame('REB260001', $example);
    }

    public function test_pattern_without_prefix(): void
    {
        $example = $this->generateExample('YYYY-000', 2026);
        $this->assertSame('2026-001', $example);
    }

    public function test_pattern_with_5_zeros(): void
    {
        $example = $this->generateExample('REB-YYYY-00000', 2026);
        $this->assertSame('REB-2026-00001', $example);
    }

    public function test_valid_pattern_with_zeros(): void
    {
        $this->assertTrue($this->isValidPattern('AAAYYYY0000'));
        $this->assertTrue($this->isValidPattern('000'));
        $this->assertTrue($this->isValidPattern('PREFIX-0000'));
    }

    public function test_invalid_pattern_without_zeros(): void
    {
        $this->assertFalse($this->isValidPattern('AAAYYYY'));
        $this->assertFalse($this->isValidPattern('PREFIX'));
        $this->assertFalse($this->isValidPattern(''));
    }
}
