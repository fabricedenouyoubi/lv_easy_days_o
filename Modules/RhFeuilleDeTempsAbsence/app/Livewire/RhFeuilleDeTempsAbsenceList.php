<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Component;

class RhFeuilleDeTempsAbsenceList extends Component
{
    public $showAddAbsenceModal = false;

    public function toogle_add_absence_modal()
    {
        $this->showAddAbsenceModal = !$this->showAddAbsenceModal;
    }


    public function render()
    {
        return view('rhfeuilledetempsabsence::livewire.rh-feuille-de-temps-absence-list');
    }
}
