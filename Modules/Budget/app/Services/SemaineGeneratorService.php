<?php

namespace Modules\Budget\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;
use Modules\Entreprise\Models\Entreprise;

class SemaineGeneratorService
{
    /**
     * Générer toutes les feuilles de temps pour une année financière
     */
    public function generateFeuillesDeTemps(AnneeFinanciere $anneeFinanciere)
    {
        DB::transaction(function () use ($anneeFinanciere) {

            // Récupérer le premier jour de la semaine depuis l'entreprise
            $entreprise = Entreprise::first();
            $premierJourSemaine = $entreprise ? $entreprise->premier_jour_semaine : 1;

            $currentDate = $anneeFinanciere->debut->copy();
            $weekNumber = 1;
            // Ajuster la date de début pour commencer au premier jour de la semaine défini
            $currentDate = $this->ajusterAuPremierJourSemaine($currentDate, $premierJourSemaine);

            while ($currentDate->lte($anneeFinanciere->fin)) {
                // Calculer la date de fin de semaine (6 jours après le début)
            $endOfWeek = $currentDate->copy()->addDays(6);

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
            // Passer à la semaine suivante
            $currentDate = $endOfWeek->copy()->addDay();
            }
        });

        return $this;
    }

    /**
 * Ajuster une date au premier jour de la semaine défini
 */
private function ajusterAuPremierJourSemaine(Carbon $date, int $premierJourSemaine)
{
    // Convertir le premier jour de l'entreprise en jour Carbon
    // Carbon: 0=Dimanche, 1=Lundi, 2=Mardi, etc.
    // Notre système: 1=Lundi, 2=Mardi, ..., 7=Dimanche
    $carbonPremierJour = $premierJourSemaine === 7 ? 0 : $premierJourSemaine;
    
    // Calculer le nombre de jours à reculer pour atteindre le premier jour de la semaine
    $jourActuel = $date->dayOfWeek;
    $joursAReculer = ($jourActuel - $carbonPremierJour + 7) % 7;
    
    return $date->copy()->subDays($joursAReculer);
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
