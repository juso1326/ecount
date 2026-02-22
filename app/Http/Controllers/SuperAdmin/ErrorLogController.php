<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ErrorLogController extends Controller
{
    private string $centralLogPath;

    public function __construct()
    {
        $this->centralLogPath = storage_path('logs/laravel.log');
    }

    public function index(Request $request)
    {
        $level    = $request->input('level', '');
        $search   = $request->input('search', '');
        $tenantId = $request->input('tenant', '');  // '' = 中央 log
        $perPage  = 50;

        // 取得所有租戶清單（含 log 檔是否存在）
        $tenants = Tenant::orderBy('id')->get()->map(function ($t) {
            $path = storage_path("logs/tenant_{$t->id}.log");
            return [
                'id'       => $t->id,
                'name'     => $t->name,
                'has_log'  => File::exists($path),
                'log_size' => File::exists($path) ? $this->formatSize(File::size($path)) : '0 B',
            ];
        });

        // 決定讀哪個 log
        if ($tenantId) {
            $logPath = storage_path("logs/tenant_{$tenantId}.log");
            $logLabel = "租戶：{$tenantId}";
        } else {
            $logPath  = $this->centralLogPath;
            $logLabel = '中央系統';
        }

        $entries = $this->parseLog($logPath, $level, $search, $perPage);
        $levels  = ['ERROR', 'WARNING', 'INFO', 'DEBUG', 'CRITICAL', 'ALERT', 'EMERGENCY', 'NOTICE'];
        $logSize = File::exists($logPath)
            ? $this->formatSize(File::size($logPath))
            : '0 B';

        // 各 level 計數（不篩選時的全量統計）
        $allEntries  = $this->parseLog($logPath, '', '', 5000);
        $levelCounts = array_count_values(array_column($allEntries, 'level'));

        return view('superadmin.error-log.index', compact(
            'entries', 'levels', 'logSize', 'level', 'search',
            'tenants', 'tenantId', 'logLabel', 'levelCounts'
        ));
    }

    public function clear(Request $request)
    {
        $tenantId = $request->input('tenant', '');
        $logPath  = $tenantId
            ? storage_path("logs/tenant_{$tenantId}.log")
            : $this->centralLogPath;

        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        return back()->with('success', '日誌已清空');
    }

    private function parseLog(string $logPath, string $level = '', string $search = '', int $limit = 50): array
    {
        if (!File::exists($logPath)) {
            return [];
        }

        $content = File::get($logPath);
        $lines   = explode("\n", $content);

        $entries = [];
        $current = null;

        foreach ($lines as $line) {
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

        $entries = array_reverse($entries);

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
