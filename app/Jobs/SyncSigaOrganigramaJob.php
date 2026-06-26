<?php

namespace App\Jobs;

use App\Services\Integrations\SigaSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncSigaOrganigramaJob implements ShouldQueue
{
    use Queueable;

    public function handle(SigaSyncService $service): void
    {
        $service->syncOrganigrama(null, 'automatico');
        $service->syncPersonal(null, 'automatico');
    }
}
