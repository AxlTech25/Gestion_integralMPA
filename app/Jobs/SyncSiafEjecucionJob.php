<?php

namespace App\Jobs;

use App\Services\Integrations\SiafSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncSiafEjecucionJob implements ShouldQueue
{
    use Queueable;

    public function handle(SiafSyncService $service): void
    {
        $service->syncEjecucion(null, 'automatico');
    }
}
