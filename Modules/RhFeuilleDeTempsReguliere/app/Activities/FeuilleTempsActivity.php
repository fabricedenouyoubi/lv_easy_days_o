<?php

namespace Modules\RhFeuilleDeTempsReguliere\Activities;

use Illuminate\Support\Facades\Log;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Workflow\Activity;

class FeuilleTempsActivity extends Activity
{
    public function execute($operation = null, $transition, $workflowData, $insertData = null)
    {
        try {
            if ($operation == null && $insertData != null) {
                // Création d'une nouvelle opération
                $operation = Operation::create($insertData);
                $operation->applyTransition($transition, $workflowData);
            } else {
                // Mise à jour d'une opération existante
                $operation->applyTransition($transition, $workflowData);
            }

            Log::channel('daily')->info('FeuilleTempsWorkflow::execute appelée avec:', [
                'operation' => $operation->id ?? 'null',
                'transition_value' => $transition,
                'workflowData' => $workflowData,
                'insertData' => $insertData ?? 'null',
            ]);

        } catch (\Throwable $th) {
            Log::channel('daily')->error('Erreur lors du lancement du workflow de feuille de temps: ' . $th->getMessage(), [
                'insertData' => $insertData, 
                'operation' => $operation->id ?? 'null'
            ]);
            
            throw $th; // Re-lancer l'exception pour que le workflow puisse la détecter
        }

        return $operation;
    }
}