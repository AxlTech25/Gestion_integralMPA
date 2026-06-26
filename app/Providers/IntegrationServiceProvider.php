<?php

namespace App\Providers;

use App\Contracts\Integrations\SiafClientInterface;
use App\Contracts\Integrations\SigaClientInterface;
use App\Integrations\Siaf\SiafApiClient;
use App\Integrations\Siaf\SiafSimulatorClient;
use App\Integrations\Siga\SigaApiClient;
use App\Integrations\Siga\SigaSimulatorClient;
use Illuminate\Support\ServiceProvider;

class IntegrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SigaClientInterface::class, function () {
            return match (config('integrations.siga.driver')) {
                'api' => new SigaApiClient,
                default => new SigaSimulatorClient,
            };
        });

        $this->app->singleton(SiafClientInterface::class, function () {
            return match (config('integrations.siaf.driver')) {
                'api' => new SiafApiClient,
                default => new SiafSimulatorClient,
            };
        });
    }
}
