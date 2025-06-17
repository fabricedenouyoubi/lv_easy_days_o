<?php

namespace Modules\Entreprise\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Entreprise\Models\Entreprise;

class EntrepriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Entreprise::create([
            'name' => 'TCRI Canada',
            'description' => 'La table de concertation des organismes au service des personnes réfugiées et immigrantes (TCRI) est un regroupement de plus de 150 organismes œuvrant auprès des personnes réfugiées, immigrantes et sans statut au Québec.'
        ]);
    }
}
