<?php

namespace App\Services\Documentaria;

use App\Models\Documento;
use App\Models\SelloInstitucional;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class SelloService
{
    public function resolverParaUnidad(?int $unidadId): ?SelloInstitucional
    {
        if ($unidadId) {
            $sello = SelloInstitucional::query()
                ->where('activo', true)
                ->where('unidad_id', $unidadId)
                ->orderByDesc('id')
                ->first();

            if ($sello) {
                return $sello;
            }
        }

        return SelloInstitucional::query()
            ->where('activo', true)
            ->whereNull('unidad_id')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Superpone sello en PDF y devuelve path relativo al disk local, o null si no es PDF.
     */
    public function aplicarSelloDocumento(Documento $documento, ?SelloInstitucional $sello, string $codigoExpediente): ?string
    {
        if (! $documento->archivo_path || ! $sello) {
            return null;
        }

        $sourcePath = Storage::disk('local')->path($documento->archivo_path);
        if (! is_file($sourcePath) || ! $this->esPdf($sourcePath)) {
            return null;
        }

        $sealPath = Storage::disk('local')->path($sello->imagen_path);
        if (! is_file($sealPath)) {
            return null;
        }

        $outputRelative = "documentos/{$documento->expediente_id}/firmado_{$documento->id}.pdf";
        $outputPath = Storage::disk('local')->path($outputRelative);
        $dir = dirname($outputPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($sourcePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $template = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($template);
            $orientation = $size['orientation'] ?? 'P';
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($template);

            if ($pageNo === $pageCount) {
                $w = (float) $size['width'];
                $h = (float) $size['height'];
                $sealW = min(28, $w * 0.2);
                $this->colocarImagen($pdf, $sealPath, $w - $sealW - 8, $h - $sealW - 8, $sealW);
                $pdf->SetFont('Helvetica', '', 8);
                $pdf->SetXY(8, $h - 10);
                $pdf->Cell(0, 8, "{$codigoExpediente} · ".now()->format('d/m/Y H:i'));
            }
        }

        $pdf->Output('F', $outputPath);

        return $outputRelative;
    }

    public function textoSello(?SelloInstitucional $sello, string $codigoExpediente): string
    {
        $nombre = $sello?->nombre ?? 'Municipalidad Provincial de Acobamba';

        return "{$nombre} · {$codigoExpediente} · ".now()->format('d/m/Y H:i');
    }

    private function esPdf(string $path): bool
    {
        $handle = fopen($path, 'rb');
        if (! $handle) {
            return false;
        }
        $header = fread($handle, 5);
        fclose($handle);

        return $header === '%PDF-';
    }

    private function colocarImagen(Fpdi $pdf, string $imagePath, float $x, float $y, float $w): void
    {
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        try {
            if ($ext === 'png') {
                $pdf->Image($imagePath, $x, $y, $w);
            } else {
                $pdf->Image($imagePath, $x, $y, $w);
            }
        } catch (\Throwable) {
            // Sin imagen válida: el texto del expediente en el PDF sigue aplicando.
        }
    }
}
