<?php

namespace Modules\Entreprise\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Entreprise\Models\Adresse;
use Modules\Entreprise\Models\Entreprise;
use Modules\Entreprise\Models\Site;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les entreprises et adresses
        $tcri = Entreprise::where('name', 'TCRI Canada')->first();
        $centreVaudreuil = Entreprise::where('name', 'Centre d\'accueil Vaudreuil')->first();
        $servicesMontreal = Entreprise::where('name', 'Services d\'aide Montréal')->first();
        $maisonRiveSud = Entreprise::where('name', 'Maison internationale de la Rive-Sud')->first();

        $adresses = Adresse::all();

        // Sites pour TCRI Canada
        Site::create([
            'name' => 'Siège social TCRI',
            'description' => 'Bureau principal de la Table de concertation des organismes au service des personnes réfugiées et immigrantes. Centre administratif et de coordination des activités.',
            'entreprise_id' => $tcri->id,
            'adresse_id' => $adresses[0]->id 
        ]);

        Site::create([
            'name' => 'Centre Ville-Marie',
            'description' => 'Centre de services directs aux clients offrant des services d\'orientation, d\'accompagnement et de formation pour nouveaux arrivants.',
            'entreprise_id' => $tcri->id,
            'adresse_id' => $adresses[2]->id 
        ]);

        Site::create([
            'name' => 'Centre administratif',
            'description' => 'Bureau de gestion des ressources humaines, comptabilité et administration générale.',
            'entreprise_id' => $tcri->id,
            'adresse_id' => $adresses[3]->id 
        ]);
    }
}
