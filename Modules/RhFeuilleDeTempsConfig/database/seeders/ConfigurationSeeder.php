<?php

namespace Modules\RhFeuilleDeTempsConfig\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Budget\Services\AnneeFinanciereSessionService;
use Modules\Rh\Models\Employe\Employe;
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
        $anneeFinanciere = AnneeFinanciereSessionService::getAnneeCourante();

        $this->command->info('Création des configurations de test...');
        // Seeder pour les JOURS FÉRIÉS (Jour)
        // $this->seedJoursFeries($anneeFinanciere);

        // Seeder pour INDIVIDUEL 
        $this->seedIndividuel($anneeFinanciere);

        $this->command->info('Configurations créées avec succès !');
    
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
