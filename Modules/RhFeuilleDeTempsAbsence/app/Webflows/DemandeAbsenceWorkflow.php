<?php

namespace Modules\RhFeuilleDeTempsAbsence\Workflows;

use Illuminate\Support\Facades\Log;
use Modules\RhFeuilleDeTempsAbsence\Activities\DemandeAbsenceActivity;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Workflow\ActivityStub;
use Workflow\Workflow;

class DemandeAbsenceWorkflow extends Workflow
{
    public function execute($demande = null, $transition, $workflowData, $insertData = null)
    {

        return yield ActivityStub::make(DemandeAbsenceActivity::class, $demande, $transition, $workflowData, $insertData);
    }
}
