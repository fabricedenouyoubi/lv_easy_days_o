<?php

namespace Modules\RhFeuilleDeTempsAbsence\Activities;

use Illuminate\Support\Facades\Log;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Workflow\Activity;

class DemandeAbsenceActivity extends Activity
{
    public function execute($demande = null, $transition, $workflowData, $insertData = null)
    {
        try {
            if ($demande == null && $insertData != null) {
                DemandeAbsence::create($insertData)->applyTransition($transition, $workflowData);
            } else {
                $demande->applyTransition($transition, $workflowData);
            }

            Log::channel('daily')->info('DemandeAbsenceWorkflow::execute appelée avec:', [
                'demande' => $demande->id ?? 'null',
                'transition_value' => $transition,
                'workflowData' => $workflowData,
                'insertData' => $insertData ?? 'null',
            ]);

        } catch (\Throwable $th) {
            Log::channel('daily')->error('Erreur lors du lancement du workflow' . $th->getMessage(), ['insertData' => $insertData, 'demandeAbsence' => $demande?->id]);
        }

        return;
    }
}
