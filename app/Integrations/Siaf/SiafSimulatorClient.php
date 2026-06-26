<?php

namespace App\Integrations\Siaf;

use App\Contracts\Integrations\SiafClientInterface;
use Illuminate\Support\Facades\File;

class SiafSimulatorClient implements SiafClientInterface
{
    public function getEjecucionPresupuestal(string $periodo): array
    {
        $data = $this->loadFixture('ejecucion.json');
        $data['periodo'] = $periodo;

        if (! isset($data['porcentaje_ejecucion']) && isset($data['pim'], $data['ejecucion_total']) && (float) $data['pim'] > 0) {
            $data['porcentaje_ejecucion'] = round(((float) $data['ejecucion_total'] / (float) $data['pim']) * 100, 2);
        }

        return $data;
    }

    public function esSimulacion(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    private function loadFixture(string $filename): array
    {
        $path = config('integrations.siaf.fixtures_path').DIRECTORY_SEPARATOR.$filename;

        if (! File::exists($path)) {
            throw new \RuntimeException("Fixture SIAF no encontrado: {$path}");
        }

        $decoded = json_decode(File::get($path), true);

        if (! is_array($decoded)) {
            throw new \RuntimeException("Fixture SIAF inválido: {$filename}");
        }

        return $decoded;
    }
}
