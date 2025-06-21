<?php

namespace Modules\RhFeuilleDeTempsConfig\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Rh\Models\Employe\Employe;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer l'année financière active
        $anneeFinanciere = AnneeFinanciere::where('actif', true)->first();

        $this->command->info('Création des configurations de test...');
        // Seeder pour les JOURS FÉRIÉS (Jour)
        $this->seedJoursFeries($anneeFinanciere);

        // Seeder pour INDIVIDUEL 
        $this->seedIndividuel($anneeFinanciere);

        $this->command->info('Configurations créées avec succès !');
    
    }

         /**
     * Seeder pour les jours fériés (comportement Jour)
     */
    private function seedJoursFeries($anneeFinanciere)
    {
        $this->command->info('Création des jours fériés...');

        // Trouver un code de travail avec valeur_config = 'Jour'
        $codeTravailJour = CodeTravail::whereHas('categorie', function($query) {
            $query->where('configurable', true)->where('valeur_config', 'Jour');
        })->first();

        if (!$codeTravailJour) {
            $this->command->warn('Aucun code de travail avec valeur_config "Jour" trouvé. Création d\'un exemple...');
            
            // Créer une catégorie et un code de travail pour l'exemple
            $categorieJour = Categorie::firstOrCreate([
                'intitule' => 'Fermé',
                'configurable' => true,
                'valeur_config' => 'Jour'
            ]);

            $codeTravailJour = CodeTravail::firstOrCreate([
                'code' => 'FERIE',
                'libelle' => 'Jours fériés',
                'categorie_id' => $categorieJour->id
            ]);
        }

        // Créer les jours fériés
        $joursFeries = [
            [
                'libelle' => 'Jour de l\'An',
                'date' => $anneeFinanciere->debut->copy()->month(1)->day(1),
                'commentaire' => 'Premier jour de l\'année'
            ],
            [
                'libelle' => 'Fête du Travail',
                'date' => $anneeFinanciere->debut->copy()->month(5)->day(1),
                'commentaire' => 'Fête internationale des travailleurs'
            ],
            [
                'libelle' => 'Fête Nationale',
                'date' => $anneeFinanciere->debut->copy()->month(5)->day(20),
                'commentaire' => 'Fête nationale du Cameroun'
            ],
            [
                'libelle' => 'Jour de Noël',
                'date' => $anneeFinanciere->debut->copy()->month(12)->day(25),
                'commentaire' => 'Célébration de la naissance de Jésus'
            ]
        ];

        foreach ($joursFeries as $jourFerie) {
            Configuration::firstOrCreate([
                'code_travail_id' => $codeTravailJour->id,
                'annee_budgetaire_id' => $anneeFinanciere->id,
                'date' => $jourFerie['date'],
                'employe_id' => null
            ], [
                'libelle' => $jourFerie['libelle'],
                'quota' => 0,
                'consomme' => 0,
                'reste' => 0,
                'commentaire' => $jourFerie['commentaire']
            ]);
        }

        $this->command->info(' jours fériés créés');
    }

    /**
     * Seeder pour les configurations individuelles
     */
    private function seedIndividuel($anneeFinanciere)
    {
        $this->command->info('Création des configurations individuelles...');

        // Trouver des codes de travail avec valeur_config = 'Individuel'
        $codesTravailIndividuel = CodeTravail::whereHas('categorie', function($query) {
            $query->where('configurable', true)->where('valeur_config', 'Individuel');
        })->take(2)->get();

/*         if ($codesTravailIndividuel->count() < 2) {
            $this->command->warn('Pas assez de codes de travail "Individuel". Création d\'exemples...');
            
            // Créer une catégorie et des codes de travail pour l'exemple
            $categorieIndividuel = Categorie::firstOrCreate([
                'intitule' => 'Congé',
                'configurable' => true,
                'valeur_config' => 'Individuel'
            ]);

            $codesExemples = [
                ['code' => 'VAC', 'libelle' => 'Vacances'],
                ['code' => 'CONMO', 'libelle' => 'Congés Mobiles']
            ];

            foreach ($codesExemples as $codeData) {
                $codesTravailIndividuel->push(
                    CodeTravail::firstOrCreate([
                        'code' => $codeData['code'],
                        'libelle' => $codeData['libelle'],
                        'categorie_id' => $categorieIndividuel->id
                    ])
                );
            }
        } */

        // Récupérer des employés
        $employes = Employe::take(6)->get();

        if ($employes->count() < 2) {
            $this->command->error('Pas assez d\'employés dans la base. Veuillez d\'abord créer des employés.');
            return;
        }

        // Différents quotas d'heures
        $quotas = [24, 32, 40, 16]; 
        $configCount = 0;

        foreach ($codesTravailIndividuel->take(2) as $index => $codeTravail) {
            foreach ($employes->take(2) as $employeIndex => $employe) {
                $quota = $quotas[($index * 2 + $employeIndex) % count($quotas)];
                $consomme = rand(0, $quota * 0.3);
                
                Configuration::firstOrCreate([
                    'code_travail_id' => $codeTravail->id,
                    'annee_budgetaire_id' => $anneeFinanciere->id,
                    'employe_id' => $employe->id,
                    'date' => null
                ], [
                    'libelle' => $employe->nom . ' ' . $employe->prenom,
                    'quota' => $quota,
                    'consomme' => $consomme,
                    'reste' => $quota - $consomme,
                    'commentaire' => 'Configuration individuelle pour ' . $employe->prenom
                ]);
                
                $configCount++;
            }
        }

        $this->command->info('Configurations individuelles créées');
    }
}
