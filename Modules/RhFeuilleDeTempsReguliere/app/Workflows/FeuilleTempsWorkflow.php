<?php

namespace Modules\RhFeuilleDeTempsReguliere\Workflows;

use Illuminate\Support\Facades\Log;
use Modules\RhFeuilleDeTempsReguliere\Activities\FeuilleTempsActivity;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Workflow\ActivityStub;
use Workflow\Workflow;

class FeuilleTempsWorkflow extends Workflow
{
    public function execute($operation = null, $transition, $workflowData, $insertData = null)
    {
        return yield ActivityStub::make(FeuilleTempsActivity::class, $operation, $transition, $workflowData, $insertData);
    }
}