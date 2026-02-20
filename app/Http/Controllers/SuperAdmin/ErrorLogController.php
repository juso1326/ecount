<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ErrorLogController extends Controller
{
    private string $logPath;

    public function __construct()
    {
        $this->logPath = storage_path('logs/laravel.log');
    }

    public function index(Request $request)
    {
        $level  = $request->input('level', '');
        $search = $request->input('search', '');
        $perPage = 50;

        $entries = $this->parseLog($level, $search, $perPage);

        $levels = ['ERROR', 'WARNING', 'INFO', 'DEBUG', 'CRITICAL', 'ALERT', 'EMERGENCY', 'NOTICE'];

        $logSize = File::exists($this->logPath)
            ? $this->formatSize(File::size($this->logPath))
            : '0 B';

        return view('superadmin.error-log.index', compact('entries', 'levels', 'logSize', 'level', 'search'));
    }

    public function clear()
    {
        if (File::exists($this->logPath)) {
            File::put($this->logPath, '');
        }

        return back()->with('success', '日誌已清空');
    }

    private function parseLog(string $level = '', string $search = '', int $limit = 50): array
    {
        if (!File::exists($this->logPath)) {
            return [];
        }

        $content = File::get($this->logPath);
        $lines   = explode("\n", $content);

        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            // New log entry: [2026-01-01 12:00:00] env.LEVEL: message
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+): (.+)$/', $line, $m)) {
                if ($current) {
                    $entries[] = $current;
                }
                $current = [
                    'time'    => $m[1],
                    'level'   => strtoupper($m[2]),
                    'message' => $m[3],
                    'detail'  => '',
                ];
            } elseif ($current && trim($line) !== '') {
                $current['detail'] .= $line . "\n";
            }
        }

        if ($current) {
            $entries[] = $current;
        }

        // Most recent first
        $entries = array_reverse($entries);

        // Filter
        if ($level) {
            $entries = array_filter($entries, fn($e) => $e['level'] === strtoupper($level));
        }
        if ($search) {
            $entries = array_filter($entries, fn($e) =>
                stripos($e['message'], $search) !== false ||
                stripos($e['detail'], $search) !== false
            );
        }

        return array_slice(array_values($entries), 0, $limit);
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
