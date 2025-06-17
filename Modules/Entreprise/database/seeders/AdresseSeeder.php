<?php

namespace Modules\Entreprise\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Entreprise\Models\Adresse;

class AdresseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Siège social
        Adresse::create([
            'ville' => 'Montréal',
            'rue' => '1610 rue Sainte-Catherine Ouest',
            'appartement' => 'Bureau 401',
            'code_postal' => 'H3H 2S2',
            'telephone' => '514-272-2532',
            'telephone_pro' => '514-272-2532',
            'telephone_pro_ext' => '101'
        ]);

        Adresse::create([
            'ville' => 'Vaudreuil-Dorion',
            'rue' => '3000 boulevard de la Gare',
            'appartement' => 'Suite 200',
            'code_postal' => 'J7V 0H1',
            'telephone' => '450-455-3636',
            'telephone_pro' => '450-455-3636',
            'telephone_pro_ext' => '201'
        ]);

        Adresse::create([
            'ville' => 'Montréal',
            'rue' => '1001 rue Sherbrooke Est',
            'appartement' => '3e étage',
            'code_postal' => 'H2L 1L3',
            'telephone' => '514-598-7722',
            'telephone_pro' => '514-598-7722',
            'telephone_pro_ext' => '301'
        ]);

        Adresse::create([
            'ville' => 'Montréal',
            'rue' => '4388 rue Saint-Denis',
            'appartement' => '2e étage',
            'code_postal' => 'H2J 2L1',
            'telephone' => '514-844-7373',
            'telephone_pro' => '514-844-7373',
            'telephone_pro_ext' => '701'
        ]);
    }
}
