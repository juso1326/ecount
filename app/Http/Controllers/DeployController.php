<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    public function handle(Request $request)
    {
        $secret = config('app.deploy_secret');

        if (empty($secret) || $request->header('X-DEPLOY-TOKEN') !== $secret) {
            Log::warning('Deploy webhook: unauthorized attempt', [
                'ip' => $request->ip(),
            ]);
            abort(403, 'Unauthorized');
        }

        $script = base_path('deploy.sh');

        if (!file_exists($script)) {
            return response()->json(['status' => 'error', 'message' => 'deploy.sh not found'], 500);
        }

        exec('bash ' . escapeshellarg($script) . ' 2>&1', $output, $exitCode);

        Log::info('Deploy webhook executed', ['exit_code' => $exitCode]);

        return response()->json([
            'status' => $exitCode === 0 ? 'ok' : 'error',
            'output' => $output,
        ], $exitCode === 0 ? 200 : 500);
    }
}
