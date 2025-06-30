<?php

namespace Modules\RhFeuilleDeTempsAbsence\Workflows;

use Illuminate\Support\Facades\Log;
use Modules\RhFeuilleDeTempsAbsence\Activities\DemandeAbsenceActivity;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Workflow\ActivityStub;
use Workflow\Workflow;

class DemandeAbsenceWorkflow extends Workflow
{
    public function execute(DemandeAbsence $demande, string $ancienStatut, string $nouveauStatut, $commentaire = null)
    {
        // Log avant la mise à jour
        /* Log::info('Ancien Statut: ' . $ancienStatut . ' Nouveau Statut: ' . $nouveauStatut);
        Log::info($demande->workflow_log); */

       return yield ActivityStub::make(DemandeAbsenceActivity::class, $demande, $ancienStatut, $nouveauStatut, $commentaire);

    }
}
