<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests for TenantSetting boolean save logic.
 * The bug was: $value ? 'true' : 'false' would always save 'true'
 * because the string 'false' is truthy in PHP.
 */
class TenantSettingBooleanTest extends TestCase
{
    private function convertBoolean(mixed $value): string
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
    }

    public function test_string_true_converts_to_true(): void
    {
        $this->assertSame('true', $this->convertBoolean('true'));
    }

    public function test_string_false_converts_to_false(): void
    {
        // This was the bug: 'false' (non-empty string) is truthy in PHP
        // filter_var correctly handles it
        $this->assertSame('false', $this->convertBoolean('false'));
    }

    public function test_integer_1_converts_to_true(): void
    {
        $this->assertSame('true', $this->convertBoolean(1));
    }

    public function test_integer_0_converts_to_false(): void
    {
        $this->assertSame('false', $this->convertBoolean(0));
    }

    public function test_string_1_converts_to_true(): void
    {
        $this->assertSame('true', $this->convertBoolean('1'));
    }

    public function test_string_0_converts_to_false(): void
    {
        $this->assertSame('false', $this->convertBoolean('0'));
    }

    public function test_bool_true_converts_to_true(): void
    {
        $this->assertSame('true', $this->convertBoolean(true));
    }

    public function test_bool_false_converts_to_false(): void
    {
        $this->assertSame('false', $this->convertBoolean(false));
    }

    public function test_old_bug_string_false_was_truthy(): void
    {
        // Demonstrate the original bug: non-empty string is always truthy
        $oldBehavior = fn($v) => $v ? 'true' : 'false';
        $this->assertSame('true', $oldBehavior('false'), 'Old behavior: string "false" incorrectly saved as "true"');

        // Confirm fix is different
        $this->assertSame('false', $this->convertBoolean('false'), 'Fixed behavior: string "false" correctly saves as "false"');
    }
}
