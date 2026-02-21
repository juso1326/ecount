<?php

namespace App\Bootstrappers;

use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class TenantLoggingBootstrapper implements TenancyBootstrapper
{
    protected ?string $originalChannel = null;

    public function __construct(protected LogManager $logManager) {}

    public function bootstrap(Tenant $tenant): void
    {
        $this->originalChannel = config('logging.default');

        $tenantId  = $tenant->getTenantKey();
        $logPath   = storage_path("logs/tenant_{$tenantId}.log");
        $channel   = "tenant_{$tenantId}";

        // 動態注冊租戶專屬 log channel
        config([
            "logging.channels.{$channel}" => [
                'driver' => 'single',
                'path'   => $logPath,
                'level'  => 'debug',
                'days'   => 14,
            ],
            'logging.default' => $channel,
        ]);

        // 清除 log manager 快取，讓新 channel 生效
        $this->logManager->forgetChannel();
    }

    public function revert(): void
    {
        if ($this->originalChannel) {
            config(['logging.default' => $this->originalChannel]);
            $this->logManager->forgetChannel();
        }
    }
}
