<?php

namespace App\Integrations\Siaf;

use App\Contracts\Integrations\SiafClientInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SiafApiClient implements SiafClientInterface
{
    public function getEjecucionPresupuestal(string $periodo): array
    {
        $config = config('integrations.siaf.api');
        $baseUrl = rtrim((string) ($config['base_url'] ?? ''), '/');

        if ($baseUrl === '') {
            throw new \RuntimeException('SIAF_API_BASE_URL no configurada.');
        }

        $attempts = max(1, (int) ($config['retries'] ?? 3));
        $lastException = null;

        for ($i = 0; $i < $attempts; $i++) {
            try {
                $response = Http::withToken((string) ($config['token'] ?? ''))
                    ->timeout((int) ($config['timeout'] ?? 30))
                    ->acceptJson()
                    ->get($baseUrl.'/api/v1/ejecucion', ['periodo' => $periodo]);

                if ($response->failed()) {
                    throw new \RuntimeException('SIAF API respondió con error HTTP '.$response->status());
                }

                return $response->json() ?? [];
            } catch (ConnectionException $e) {
                $lastException = $e;
                usleep(200000 * ($i + 1));
            }
        }

        throw new \RuntimeException(
            'No se pudo conectar con SIAF: '.($lastException?->getMessage() ?? 'error desconocido')
        );
    }

    public function esSimulacion(): bool
    {
        return false;
    }
}
