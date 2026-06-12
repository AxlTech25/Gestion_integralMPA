<?php

namespace Database\Seeders;

use App\Models\SelloInstitucional;
use App\Models\UnidadOrganizacional;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SellosInstitucionalesSeeder extends Seeder
{
    public function run(): void
    {
        $relativePath = 'sellos/sello_mpa.jpg';
        $disk = Storage::disk('local');

        if (! $disk->exists($relativePath)) {
            $disk->makeDirectory('sellos');
            $jpeg = base64_decode(
                '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAAQABADASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAf/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAB//2Q==',
                true
            );
            $disk->put($relativePath, $jpeg);
        }

        SelloInstitucional::updateOrCreate(
            ['nombre' => 'Sello Municipal MPA'],
            [
                'unidad_id' => null,
                'imagen_path' => $relativePath,
                'activo' => true,
                'vigente_desde' => now()->startOfYear(),
            ]
        );

        $utisId = UnidadOrganizacional::where('codigo_org', 'ORG-061')->value('id');
        if ($utisId) {
            SelloInstitucional::updateOrCreate(
                ['nombre' => 'Sello UTIS'],
                [
                    'unidad_id' => $utisId,
                    'imagen_path' => $relativePath,
                    'activo' => true,
                    'vigente_desde' => now()->startOfYear(),
                ]
            );
        }
    }
}
