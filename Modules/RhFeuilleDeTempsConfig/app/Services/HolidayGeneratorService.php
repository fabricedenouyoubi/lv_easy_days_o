<?php

namespace Modules\RhFeuilleDeTempsConfig\Services;

use Spatie\Holidays\Holidays;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HolidayGeneratorService
{
    /**
     * Générer les jours fériés canadiens pour une année financière
     */
    public function generateHolidaysForYear(AnneeFinanciere $anneeFinanciere, CodeTravail $codeTravail)
    {
        try {
            DB::beginTransaction();

            // Vérifier si les jours fériés existent déjà
            $existingHolidays = Configuration::joursFeries()
                ->forCodeTravail($codeTravail->id)
                ->forAnnee($anneeFinanciere->id)
                ->count();

            if ($existingHolidays > 0) {
                return [
                    'success' => false,
                    'message' => 'Les jours fériés ont déjà été générés pour cette année.',
                    'count' => $existingHolidays
                ];
            }

            // Obtenir les années à traiter (année financière = avril N à mars N+1)
            $startYear = $anneeFinanciere->debut->year;
            $endYear = $anneeFinanciere->fin->year;

            $holidaysCreated = 0;
            $holidaysData = [];

            // Récupérer les jours fériés pour chaque année
            foreach ([$startYear, $endYear] as $year) {
                $holidays = $this->getHolidaysForYear($year);
                
                foreach ($holidays as $holiday) {
                    $holidayDate = Carbon::parse($holiday['date']);
                    
                    // Vérifier si la date est dans la période de l'année financière
                    if ($holidayDate->between($anneeFinanciere->debut, $anneeFinanciere->fin)) {
                        $holidaysData[] = [
                            'libelle' => $this->translateHolidayName($holiday['name']),
                            'date' => $holidayDate->format('Y-m-d'),
                            'commentaire' => "Jour férié canadien généré automatiquement",
                            'quota' => 0,
                            'consomme' => 0,
                            'reste' => 0,
                            'employe_id' => null,
                            'annee_budgetaire_id' => $anneeFinanciere->id,
                            'code_travail_id' => $codeTravail->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            // Insérer tous les jours fériés en une seule fois
            if (!empty($holidaysData)) {
                Configuration::insert($holidaysData);
                $holidaysCreated = count($holidaysData);
            }

            DB::commit();

            Log::info("Jours fériés générés avec succès", [
                'annee_financiere_id' => $anneeFinanciere->id,
                'code_travail_id' => $codeTravail->id,
                'nombre_jours' => $holidaysCreated
            ]);

            return [
                'success' => true,
                'message' => "Génération réussie : {$holidaysCreated} jours fériés créés.",
                'count' => $holidaysCreated,
                'holidays' => collect($holidaysData)->pluck('libelle', 'date')->toArray()
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Erreur lors de la génération des jours fériés", [
                'error' => $e->getMessage(),
                'annee_financiere_id' => $anneeFinanciere->id ?? null,
                'code_travail_id' => $codeTravail->id ?? null
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de la génération : ' . $e->getMessage(),
                'count' => 0
            ];
        }
    }

    /**
     * Obtenir les jours fériés pour une année donnée
     */
    private function getHolidaysForYear(int $year): array
    {
        try {
            // Utiliser le package Spatie Holidays
            $holidaysCollection = Holidays::for(country: 'ca', year: $year)->get();
            
            $holidays = [];
            foreach ($holidaysCollection as $holiday) {
                $holidays[] = [
                    'name' => $holiday['name'],
                    'date' => $holiday['date']->format('Y-m-d')
                ];
            }
            
            Log::info("Jours fériés trouvés via Spatie pour $year", ['count' => count($holidays)]);
            return $holidays;
            
        } catch (\Exception $e) {
            Log::error("Erreur avec le package Spatie pour $year: " . $e->getMessage());
            
            // Fallback : utiliser des données statiques
            Log::info("Utilisation du fallback pour les jours fériés de $year");
            return $this->getFallbackHolidays($year);
        }
    }

    /**
     * Jours fériés de fallback si le package Spatie ne fonctionne pas
     */
    private function getFallbackHolidays(int $year): array
    {
        $holidays = [];
        
        // Jours fériés fixes
        $holidays[] = ['name' => "New Year's Day", 'date' => "$year-01-01"];
        $holidays[] = ['name' => "Canada Day", 'date' => "$year-07-01"];
        $holidays[] = ['name' => "Christmas Day", 'date' => "$year-12-25"];
        $holidays[] = ['name' => "Boxing Day", 'date' => "$year-12-26"];
        
        // Fête du Travail (1er lundi de septembre)
        $labourDay = Carbon::create($year, 9, 1)->firstOfMonth()->next(Carbon::MONDAY);
        $holidays[] = ['name' => "Labour Day", 'date' => $labourDay->format('Y-m-d')];
        
        // Action de grâce (2e lundi d'octobre)
        $thanksgiving = Carbon::create($year, 10, 1)->firstOfMonth()->next(Carbon::MONDAY);
        $holidays[] = ['name' => "Thanksgiving", 'date' => $thanksgiving->format('Y-m-d')];
        
        // Jour du Souvenir (11 novembre)
        $holidays[] = ['name' => "Remembrance Day", 'date' => "$year-11-11"];
        
        // Calcul de Pâques et jours associés
        $easter = Carbon::createFromTimestamp(easter_date($year));
        $goodFriday = $easter->copy()->subDays(2);
        $easterMonday = $easter->copy()->addDay();
        
        $holidays[] = ['name' => "Good Friday", 'date' => $goodFriday->format('Y-m-d')];
        $holidays[] = ['name' => "Easter Monday", 'date' => $easterMonday->format('Y-m-d')];
        
        // Fête de la Reine/Victoria Day (dernier lundi avant le 25 mai)
        $victoriaDay = Carbon::create($year, 5, 25)->previous(Carbon::MONDAY);
        $holidays[] = ['name' => "Victoria Day", 'date' => $victoriaDay->format('Y-m-d')];
        
        // Family Day (3e lundi de février) - varie selon les provinces
        $familyDay = Carbon::create($year, 2, 1)->addWeeks(2)->next(Carbon::MONDAY);
        $holidays[] = ['name' => "Family Day", 'date' => $familyDay->format('Y-m-d')];
        
        Log::info("Jours fériés de fallback générés pour $year", ['count' => count($holidays)]);
        
        return $holidays;
    }

    /**
     * Vérifier si les jours fériés ont déjà été générés
     */
    public function hasHolidaysGenerated(AnneeFinanciere $anneeFinanciere, CodeTravail $codeTravail): bool
    {
        return Configuration::joursFeries()
            ->forCodeTravail($codeTravail->id)
            ->forAnnee($anneeFinanciere->id)
            ->exists();
    }

    /**
     * Obtenir le nombre de jours fériés existants
     */
    public function getExistingHolidaysCount(AnneeFinanciere $anneeFinanciere, CodeTravail $codeTravail): int
    {
        return Configuration::joursFeries()
            ->forCodeTravail($codeTravail->id)
            ->forAnnee($anneeFinanciere->id)
            ->count();
    }

    /**
     * Traduire les noms des jours fériés en français
     */
    private function translateHolidayName(string $englishName): string
    {
        $translations = [
            "New Year's Day" => "Jour de l'An",
            "Family Day" => "Fête de la Famille",
            "Good Friday" => "Vendredi Saint",
            "Easter Monday" => "Lundi de Pâques",
            "Victoria Day" => "Fête de la Reine",
            "Canada Day" => "Fête du Canada",
            "Civic Holiday" => "Congé Civique",
            "Labour Day" => "Fête du Travail",
            "National Day for Truth and Reconciliation" => "Journée nationale de la vérité et de la réconciliation",
            "Thanksgiving" => "Action de grâce",
            "Remembrance Day" => "Jour du Souvenir",
            "Christmas Day" => "Noël",
            "Boxing Day" => "Lendemain de Noël",
        ];

        return $translations[$englishName] ?? $englishName;
    }

    /**
     * Supprimer tous les jours fériés générés pour une année
     */
    public function deleteGeneratedHolidays(AnneeFinanciere $anneeFinanciere, CodeTravail $codeTravail): bool
    {
        try {
            $deleted = Configuration::joursFeries()
                ->forCodeTravail($codeTravail->id)
                ->forAnnee($anneeFinanciere->id)
                ->where('commentaire', 'LIKE', '%généré automatiquement%')
                ->delete();

            Log::info("Jours fériés supprimés", [
                'annee_financiere_id' => $anneeFinanciere->id,
                'code_travail_id' => $codeTravail->id,
                'nombre_supprimes' => $deleted
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression des jours fériés", [
                'error' => $e->getMessage(),
                'annee_financiere_id' => $anneeFinanciere->id,
                'code_travail_id' => $codeTravail->id
            ]);

            return false;
        }
    }

    /**
     * Obtenir un aperçu des jours fériés qui seraient générés
     */
    public function previewHolidays(AnneeFinanciere $anneeFinanciere): array
    {
        $startYear = $anneeFinanciere->debut->year;
        $endYear = $anneeFinanciere->fin->year;
        
        $preview = [];
        
        foreach ([$startYear, $endYear] as $year) {
            $holidays = $this->getHolidaysForYear($year);
            
            foreach ($holidays as $holiday) {
                $holidayDate = Carbon::parse($holiday['date']);
                
                if ($holidayDate->between($anneeFinanciere->debut, $anneeFinanciere->fin)) {
                    $preview[] = [
                        'date' => $holidayDate->format('Y-m-d'),
                        'formatted_date' => $holidayDate->format('d M Y'),
                        'day_name' => $holidayDate->translatedFormat('l'),
                        'english_name' => $holiday['name'],
                        'french_name' => $this->translateHolidayName($holiday['name']),
                    ];
                }
            }
        }
        
        // Trier par date
        usort($preview, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        return $preview;
    }
}