<?php

namespace Modules\RhFeuilleDeTempsConfig\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\RhFeuilleDeTempsConfig\Models\CodeDeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\CategorieCodeDeTravail;

class CodeDeTravailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Codes de travail de base
        $codesDeTravail = [
            // Heures régulières
            [
                'code' => 'REG',
                'libelle' => 'Heures régulières',
                'description' => 'Heures de travail normales',
                'categorie' => CategorieCodeDeTravail::HEURE_REGULIERE
            ],
            
            // Heures supplémentaires
            [
                'code' => 'SUPP',
                'libelle' => 'Heures supplémentaires',
                'description' => 'Heures de travail au-delà des heures normales',
                'categorie' => CategorieCodeDeTravail::HEURE_SUPPLEMENTAIRE
            ],
            
            // Congés
            [
                'code' => 'CONGE',
                'libelle' => 'Congé payé',
                'description' => 'Congé annuel payé',
                'categorie' => CategorieCodeDeTravail::CONGE
            ],
            [
                'code' => 'FERIE',
                'libelle' => 'Jour férié',
                'description' => 'Jours fériés officiels',
                'categorie' => CategorieCodeDeTravail::CONGE
            ],
            [
                'code' => 'MALADIE',
                'libelle' => 'Congé maladie',
                'description' => 'Congé pour maladie',
                'categorie' => CategorieCodeDeTravail::CONGE
            ],
            
            // Formation
            [
                'code' => 'FORM',
                'libelle' => 'Formation',
                'description' => 'Temps consacré à la formation',
                'categorie' => CategorieCodeDeTravail::FORMATION
            ],
            
            // Déplacement
            [
                'code' => 'DEPL',
                'libelle' => 'Déplacement',
                'description' => 'Temps de déplacement',
                'categorie' => CategorieCodeDeTravail::DEPLACEMENT
            ],
            
            // Caisse de temps
            [
                'code' => 'CAISSE',
                'libelle' => 'Caisse de temps',
                'description' => 'Utilisation de la caisse de temps',
                'categorie' => CategorieCodeDeTravail::CAISSE_TEMPS
            ],
            
            // Congé mobile
            [
                'code' => 'MOBILE',
                'libelle' => 'Congé mobile',
                'description' => 'Congé mobile/flottant',
                'categorie' => CategorieCodeDeTravail::CONGE_MOBILE
            ],
            
            // CSN
            [
                'code' => 'CSN',
                'libelle' => 'CSN',
                'description' => 'Temps CSN (Conseil syndical national)',
                'categorie' => CategorieCodeDeTravail::CSN
            ],
        ];

        foreach ($codesDeTravail as $codeTravail) {
            CodeDeTravail::updateOrCreate(
                ['code' => $codeTravail['code']], // Clé de recherche
                $codeTravail // Données à insérer/mettre à jour
            );
        }

        $this->command->info('Codes de travail de base créés avec succès.');
    }
}
