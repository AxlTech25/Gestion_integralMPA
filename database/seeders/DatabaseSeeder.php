<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            OrganigramaSeeder::class,
            RolePermisoSeeder::class,
            SellosInstitucionalesSeeder::class,
            TiposDocumentalesSeeder::class,
            SgmiSeeder::class,
            EquiposDemoSeeder::class,
            SiafDemoSeeder::class,
        ]);
    }
}
