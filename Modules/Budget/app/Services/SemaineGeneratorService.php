<?php

namespace Modules\Budget\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;

class SemaineGeneratorService
{
    /**
     * Générer toutes les feuilles de temps pour une année financière
     */
    public function generateFeuillesDeTemps(AnneeFinanciere $anneeFinanciere)
    {
        DB::transaction(function () use ($anneeFinanciere) {
            $currentDate = $anneeFinanciere->debut->copy();
            $weekNumber = 1;

            while ($currentDate->lte($anneeFinanciere->fin)) {
                // Calculer le jour de la semaine avec dimanche comme jour 0
                $adjustedWeekday = ($currentDate->dayOfWeek + 1) % 7;

                // Si ce n'est pas un dimanche, reculer au dimanche précédent
                if ($adjustedWeekday !== 0) {
                    $currentDate = $this->getPreviousSunday($currentDate);
                    $adjustedWeekday = ($currentDate->dayOfWeek + 1) % 7;
                }

                // Calculer la date de fin de semaine (samedi)
                $daysUntilEndOfWeek = 6 - $adjustedWeekday;
                $endOfWeek = $currentDate->copy()->addDays($daysUntilEndOfWeek);

                // Créer la feuille de temps pour cette semaine
                SemaineAnnee::create([
                    'numero_semaine' => $weekNumber,
                    'debut' => $currentDate->toDateString(),
                    'fin' => $endOfWeek->toDateString(),
                    'actif' => false,
                    'annee_financiere_id' => $anneeFinanciere->id,
                    'est_semaine_de_paie' => false
                ]);

                $weekNumber++;

                // Passer à la semaine suivante (dimanche suivant)
                $currentDate = $endOfWeek->copy()->addDay();
            }
        });

        return $this;
    }

    /**
     * Obtenir le dimanche précédent
     */
    private function getPreviousSunday(Carbon $currentDate)
    {
        $currentWeekday = $currentDate->dayOfWeek;
        $daysToSubtract = ($currentWeekday + 1) % 7;
        
        return $currentDate->copy()->subDays($daysToSubtract);
    }

    /**
     * Désactiver toutes les feuilles de temps
     */
    public function deactivateAllFeuillesDeTemps()
    {
        return SemaineAnnee::query()->update(['actif' => false]);
    }

    /**
     * Activer les feuilles de temps d'une année financière spécifique
     */
    public function activateFeuillesForAnnee(AnneeFinanciere $anneeFinanciere)
    {
        return SemaineAnnee::where('annee_financiere_id', $anneeFinanciere->id)
                            ->update(['actif' => true]);
    }

    /**
     * Obtenir les statistiques de génération pour une année
     */
    public function getGenerationStats(AnneeFinanciere $anneeFinanciere)
    {
        $totalFeuilles = SemaineAnnee::where('annee_financiere_id', $anneeFinanciere->id)->count();
        $feuillesActives = SemaineAnnee::where('annee_financiere_id', $anneeFinanciere->id)
                                        ->where('actif', true)
                                        ->count();
        $semainesDePaie = SemaineAnnee::where('annee_financiere_id', $anneeFinanciere->id)
                                       ->where('est_semaine_de_paie', true)
                                       ->count();

        return [
            'total_feuilles' => $totalFeuilles,
            'feuilles_actives' => $feuillesActives,
            'semaines_de_paie' => $semainesDePaie,
            'periode_debut' => $anneeFinanciere->debut->format('d/m/Y'),
            'periode_fin' => $anneeFinanciere->fin->format('d/m/Y')
        ];
    }

    /**
     * Vérifier si les feuilles ont déjà été générées pour une année
     */
    public function areFeuillesGenerated(AnneeFinanciere $anneeFinanciere)
    {
        $count = SemaineAnnee::where('annee_financiere_id', $anneeFinanciere->id)->count();
        return $count > 0;
    }

    /**
     * Supprimer toutes les feuilles d'une année pour régénération - Si necessaire
     */
    public function deleteFeuillesForAnnee(AnneeFinanciere $anneeFinanciere)
    {
        return SemaineAnnee::where('annee_financiere_id', $anneeFinanciere->id)->delete();
    }

    /**
     * Régénérer les feuilles pour une année - supprime et recrée
     */
    public function regenerateFeuillesDeTemps(AnneeFinanciere $anneeFinanciere)
    {
        DB::transaction(function () use ($anneeFinanciere) {
            // Supprimer les feuilles existantes
            $this->deleteFeuillesForAnnee($anneeFinanciere);
            
            // Régénérer
            $this->generateFeuillesDeTemps($anneeFinanciere);
        });

        return $this;
    }

    /**
     * Marquer une semaine comme semaine de paie
     */
    public function markAsSemaineDePaie($feuilleId)
    {
        return SemaineAnnee::where('id', $feuilleId)
                            ->update(['est_semaine_de_paie' => true]);
    }

    /**
     * Démarquer une semaine comme semaine de non paie
     */
    public function unmarkAsSemaineDePaie($feuilleId)
    {
        return SemaineAnnee::where('id', $feuilleId)
                            ->update(['est_semaine_de_paie' => false]);
    }
}
