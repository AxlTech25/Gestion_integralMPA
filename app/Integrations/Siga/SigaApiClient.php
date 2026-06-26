<?php

namespace App\Integrations\Siga;

use App\Contracts\Integrations\SigaClientInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SigaApiClient implements SigaClientInterface
{
    public function getPatrimonioInformatica(): array
    {
        $response = $this->request('GET', '/api/v1/patrimonio/informatica');

        return $response['items'] ?? $response['data'] ?? [];
    }

    public function getOrganigrama(): array
    {
        $response = $this->request('GET', '/api/v1/organigrama/unidades');

        return $response['unidades'] ?? $response['data'] ?? [];
    }

    public function getPersonal(): array
    {
        $response = $this->request('GET', '/api/v1/personal/activo');

        return $response['personal'] ?? $response['data'] ?? [];
    }

    public function esSimulacion(): bool
    {
        return false;
    }

    /** @return array<string, mixed> */
    private function request(string $method, string $uri): array
    {
        $config = config('integrations.siga.api');
        $baseUrl = rtrim((string) ($config['base_url'] ?? ''), '/');

        if ($baseUrl === '') {
            throw new \RuntimeException('SIGA_API_BASE_URL no configurada.');
        }

        $attempts = max(1, (int) ($config['retries'] ?? 3));
        $lastException = null;

        for ($i = 0; $i < $attempts; $i++) {
            try {
                $response = Http::withToken((string) ($config['token'] ?? ''))
                    ->timeout((int) ($config['timeout'] ?? 30))
                    ->acceptJson()
                    ->send($method, $baseUrl.$uri);

                if ($response->failed()) {
                    throw new \RuntimeException('SIGA API respondió con error HTTP '.$response->status());
                }

                return $response->json() ?? [];
            } catch (ConnectionException $e) {
                $lastException = $e;
                usleep(200000 * ($i + 1));
            }
        }

        throw new \RuntimeException(
            'No se pudo conectar con SIGA: '.($lastException?->getMessage() ?? 'error desconocido')
        );
    }
}
