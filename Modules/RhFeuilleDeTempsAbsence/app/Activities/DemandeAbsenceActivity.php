<?php

namespace Modules\RhFeuilleDeTempsAbsence\Activities;

use Illuminate\Support\Facades\Log;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Workflow\Activity;

class DemandeAbsenceActivity extends Activity
{
    public function execute(DemandeAbsence $demande, string $ancienStatut, string $nouveauStatut, $commentaire = null)
    {
        try {
                    // Log avant la mise à jour
       // Log::info('Ancien Statut: ' . $ancienStatut . ' Nouveau Statut: ' . $nouveauStatut);

        //dd($demande->workflow_log);

        $demande->statut = $nouveauStatut;
        $demande->build_workflow_log($ancienStatut, $nouveauStatut, $commentaire);
        $demande->save();

        // Log après la mise à jour
       // Log::info('Demande sauvegardée avec statut: ' . $demande->statut);
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }

        return;
    }
}
