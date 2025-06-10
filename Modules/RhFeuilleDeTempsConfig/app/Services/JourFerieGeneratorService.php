<?php

namespace Modules\RhFeuilleDeTempsConfig\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Models\CodeDeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\ConfigurationCodeDeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\CategorieCodeDeTravail;

class JourFerieGeneratorService
{
    /**
     * Jours fériés fixes au Canada
     */
    private $joursfériesFixes = [
        '01-01' => 'Jour de l\'An',
        '07-01' => 'Fête du Canada',
        '12-25' => 'Noël',
        '12-26' => 'Lendemain de Noël'
    ];

    /**
     * Générer les jours fériés pour une année financière
     */
    public function generateJourFerie(AnneeFinanciere $anneeFinanciere)
    {
        DB::transaction(function () use ($anneeFinanciere) {
            // Récupérer le code de travail pour les jours fériés
            $codeTravail = $this->getOrCreateCodeFerie();

            // Générer les jours fériés dans la période financière
            $holidayDict = $this->getHolidaysForPeriod($anneeFinanciere);

            $ferieObjects = [];
            foreach ($holidayDict as $date => $holidayName) {
                // Vérifier si ce jour férié n'existe pas déjà
                if (!$this->holidayExists($date)) {
                    $ferieObjects[] = [
                        'jour' => $date,
                        'libelle' => $holidayName,
                        'description' => $holidayName,
                        'code_de_travail_id' => $codeTravail->id,
                        'annee_financiere_id' => $anneeFinanciere->id,
                        'nombre_d_heure' => 8, // Journée standard
                        'nombre_d_heure_restant' => 8,
                        'nombre_d_heure_pris' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            if (!empty($ferieObjects)) {
                ConfigurationCodeDeTravail::insert($ferieObjects);
            }
        });

        return $this;
    }

    /**
     * Obtenir ou créer le code de travail pour les jours fériés
     */
    private function getOrCreateCodeFerie()
    {
        return CodeDeTravail::firstOrCreate(
            [
                'code' => 'FERIE',
                'categorie' => CategorieCodeDeTravail::CONGE
            ],
            [
                'libelle' => 'Jour férié',
                'description' => 'Jours fériés officiels',
            ]
        );
    }

    /**
     * Obtenir tous les jours fériés pour la période financière
     */
    private function getHolidaysForPeriod(AnneeFinanciere $anneeFinanciere)
    {
        $holidayDict = [];
        $startYear = $anneeFinanciere->debut->year;
        $endYear = $anneeFinanciere->fin->year;

        // Traiter chaque année de la période financière
        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearHolidays = $this->getHolidaysForYear($year);
            
            foreach ($yearHolidays as $date => $name) {
                $holidayDate = Carbon::parse($date);
                
                // Vérifier si la date est dans la période financière
                if ($holidayDate->gte($anneeFinanciere->debut) && 
                    $holidayDate->lte($anneeFinanciere->fin)) {
                    $holidayDict[$date] = $name;
                }
            }
        }

        return $holidayDict;
    }

    /**
     * Obtenir les jours fériés pour une année spécifique
     */
    private function getHolidaysForYear($year)
    {
        $holidays = [];

        // Jours fériés fixes
        foreach ($this->joursfériesFixes as $monthDay => $name) {
            $date = Carbon::createFromFormat('Y-m-d', $year . '-' . $monthDay);
            $holidays[$date->toDateString()] = $name;
        }

        // Jours fériés calculés
        $holidays = array_merge($holidays, [
            $this->getGoodFriday($year)->toDateString() => 'Vendredi saint',
            $this->getEasterMonday($year)->toDateString() => 'Lundi de Pâques',
            $this->getVictoriaDay($year)->toDateString() => 'Fête de la Reine/Journée nationale des patriotes',
            $this->getLaborDay($year)->toDateString() => 'Fête du Travail',
            $this->getThanksgiving($year)->toDateString() => 'Action de grâce'
        ]);

        return $holidays;
    }

    /**
     * Calculer le Vendredi saint
     */
    private function getGoodFriday($year)
    {
        $easter = Carbon::createFromTimestamp(easter_date($year));
        return $easter->subDays(2);
    }

    /**
     * Calculer le Lundi de Pâques
     */
    private function getEasterMonday($year)
    {
        $easter = Carbon::createFromTimestamp(easter_date($year));
        return $easter->addDay();
    }

    /**
     * Calculer la Fête de la Reine (dernier lundi avant le 25 mai)
     */
    private function getVictoriaDay($year)
    {
        $may25 = Carbon::create($year, 5, 25);
        
        // Trouver le lundi précédent
        while ($may25->dayOfWeek !== Carbon::MONDAY) {
            $may25->subDay();
        }
        
        return $may25;
    }

    /**
     * Calculer la Fête du Travail (premier lundi de septembre)
     */
    private function getLaborDay($year)
    {
        $september1 = Carbon::create($year, 9, 1);
        
        // Trouver le premier lundi
        while ($september1->dayOfWeek !== Carbon::MONDAY) {
            $september1->addDay();
        }
        
        return $september1;
    }

    /**
     * Calculer l'Action de grâce (deuxième lundi d'octobre)
     */
    private function getThanksgiving($year)
    {
        $october1 = Carbon::create($year, 10, 1);
        $mondayCount = 0;
        
        // Trouver le deuxième lundi
        while ($mondayCount < 2) {
            if ($october1->dayOfWeek === Carbon::MONDAY) {
                $mondayCount++;
            }
            if ($mondayCount < 2) {
                $october1->addDay();
            }
        }
        
        return $october1;
    }

    /**
     * Vérifier si un jour férié existe déjà
     */
    private function holidayExists($date)
    {
        return ConfigurationCodeDeTravail::where('jour', $date)->exists();
    }

    /**
     * Obtenir les statistiques de génération des jours fériés
     */
    public function getGenerationStats(AnneeFinanciere $anneeFinanciere)
    {
        $codeFerie = CodeDeTravail::where('code', 'FERIE')->first();
        
        if (!$codeFerie) {
            return [
                'total_jours_feries' => 0,
                'code_ferie_exists' => false
            ];
        }

        $totalJoursFeries = ConfigurationCodeDeTravail::where('annee_financiere_id', $anneeFinanciere->id)
                                                     ->where('code_de_travail_id', $codeFerie->id)
                                                     ->count();

        return [
            'total_jours_feries' => $totalJoursFeries,
            'code_ferie_exists' => true,
            'code_ferie_id' => $codeFerie->id
        ];
    }

    /**
     * Supprimer tous les jours fériés d'une année
     */
    public function deleteJoursFeriesForAnnee(AnneeFinanciere $anneeFinanciere)
    {
        $codeFerie = CodeDeTravail::where('code', 'FERIE')->first();
        
        if ($codeFerie) {
            return ConfigurationCodeDeTravail::where('annee_financiere_id', $anneeFinanciere->id)
                                            ->where('code_de_travail_id', $codeFerie->id)
                                            ->delete();
        }
        
        return 0;
    }

    /**
     * Régénérer les jours fériés pour une année
     */
    public function regenerateJourFerie(AnneeFinanciere $anneeFinanciere)
    {
        DB::transaction(function () use ($anneeFinanciere) {
            // Supprimer les jours fériés existants
            $this->deleteJoursFeriesForAnnee($anneeFinanciere);
            
            // Régénérer
            $this->generateJourFerie($anneeFinanciere);
        });

        return $this;
    }

    /**
     * Obtenir la liste des jours fériés pour une année financière
     */
    public function getJoursFeriesForAnnee(AnneeFinanciere $anneeFinanciere)
    {
        $codeFerie = CodeDeTravail::where('code', 'FERIE')->first();
        
        if (!$codeFerie) {
            return collect();
        }

        return ConfigurationCodeDeTravail::where('annee_financiere_id', $anneeFinanciere->id)
                                        ->where('code_de_travail_id', $codeFerie->id)
                                        ->orderBy('jour')
                                        ->get();
    }
}
