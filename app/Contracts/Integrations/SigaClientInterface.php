<?php

namespace App\Contracts\Integrations;

interface SigaClientInterface
{
    /** @return array<int, array<string, mixed>> */
    public function getPatrimonioInformatica(): array;

    /** @return array<int, array<string, mixed>> */
    public function getOrganigrama(): array;

    /** @return array<int, array<string, mixed>> */
    public function getPersonal(): array;

    public function esSimulacion(): bool;
}
