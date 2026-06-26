<?php

namespace App\Integrations\Siga;

use App\Contracts\Integrations\SigaClientInterface;
use Illuminate\Support\Facades\File;

class SigaSimulatorClient implements SigaClientInterface
{
    public function getPatrimonioInformatica(): array
    {
        $data = $this->loadFixture('patrimonio.json');

        return $data['items'] ?? [];
    }

    public function getOrganigrama(): array
    {
        $data = $this->loadFixture('organigrama.json');

        return $data['unidades'] ?? [];
    }

    public function getPersonal(): array
    {
        $data = $this->loadFixture('personal.json');

        return $data['personal'] ?? [];
    }

    public function esSimulacion(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    private function loadFixture(string $filename): array
    {
        $path = config('integrations.siga.fixtures_path').DIRECTORY_SEPARATOR.$filename;

        if (! File::exists($path)) {
            throw new \RuntimeException("Fixture SIGA no encontrado: {$path}");
        }

        $decoded = json_decode(File::get($path), true);

        if (! is_array($decoded)) {
            throw new \RuntimeException("Fixture SIGA inválido: {$filename}");
        }

        return $decoded;
    }
}
