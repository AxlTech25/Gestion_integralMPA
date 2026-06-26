<?php

namespace App\Contracts\Integrations;

interface SiafClientInterface
{
    /** @return array<string, mixed> */
    public function getEjecucionPresupuestal(string $periodo): array;

    public function esSimulacion(): bool;
}
