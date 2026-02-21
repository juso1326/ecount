<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class CaptchaHelper
{
    private const CHARS = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    /**
     * 產生驗證碼，儲存至 session 並回傳 SVG 字串
     */
    public static function generate(string $sessionKey = 'captcha'): string
    {
        $code = '';
        for ($i = 0; $i < 3; $i++) {
            $code .= self::CHARS[random_int(0, strlen(self::CHARS) - 1)];
        }
        Session::put($sessionKey, strtoupper($code));
        return self::renderSvg($code);
    }

    /**
     * 驗證使用者輸入，驗證後清除 session
     */
    public static function verify(string $input, string $sessionKey = 'captcha'): bool
    {
        $stored = Session::get($sessionKey);
        Session::forget($sessionKey);
        if (!$stored) return false;
        return strtoupper(trim($input)) === strtoupper($stored);
    }

    /**
     * 產生 SVG 驗證碼圖片
     */
    private static function renderSvg(string $code): string
    {
        $width = 100;
        $height = 50;
        $bgR = random_int(235, 255);
        $bgG = random_int(235, 255);
        $bgB = random_int(235, 255);
        $bg = "rgb({$bgR},{$bgG},{$bgB})";

        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='{$width}' height='{$height}'>";
        $svg .= "<rect width='{$width}' height='{$height}' rx='4' fill='{$bg}'/>";

        // 噪點線
        for ($i = 0; $i < 5; $i++) {
            $x1 = random_int(0, $width);
            $y1 = random_int(0, $height);
            $x2 = random_int(0, $width);
            $y2 = random_int(0, $height);
            $r = random_int(150, 210);
            $g = random_int(150, 210);
            $b = random_int(150, 210);
            $svg .= "<line x1='{$x1}' y1='{$y1}' x2='{$x2}' y2='{$y2}' stroke='rgb({$r},{$g},{$b})' stroke-width='1.5'/>";
        }

        // 噪點圓
        for ($i = 0; $i < 20; $i++) {
            $cx = random_int(0, $width);
            $cy = random_int(0, $height);
            $r = random_int(180, 220);
            $svg .= "<circle cx='{$cx}' cy='{$cy}' r='2' fill='rgb({$r},{$r},{$r})'/>";
        }

        // 每個字元
        $colors = ['#1e3a5f', '#6b21a8', '#065f46', '#7c2d12', '#1e40af'];
        for ($i = 0; $i < strlen($code); $i++) {
            $x = 16 + $i * 30 + random_int(-2, 2);
            $y = 36 + random_int(-6, 6);
            $rot = random_int(-20, 20);
            $color = $colors[$i % count($colors)];
            $size = random_int(22, 28);
            $svg .= "<text x='{$x}' y='{$y}' font-size='{$size}' font-family='monospace' font-weight='bold'"
                  . " fill='{$color}' transform='rotate({$rot},{$x},{$y})'>{$code[$i]}</text>";
        }

        $svg .= '</svg>';
        return $svg;
    }
}
