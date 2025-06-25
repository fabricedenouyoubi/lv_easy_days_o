<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;

class RhFeuilleDeTempsAbsenceList extends Component
{
    use WithPagination;

    public $showAddAbsenceModal = false;
    public $demandeAbsenceId = null;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'demandeAbsenceAjoute' => 'demandeAbsenceAjoute',
    ];

    //--- afficher et caher le formulaire d'ajout d'une absence
    public function toogle_add_absence_modal()
    {
        $this->showAddAbsenceModal = !$this->showAddAbsenceModal;
    }

    //--- afficher le message de creation d'une absence
    public function demandeAbsenceAjoute()
    {
        $this->showAddAbsenceModal = false;
        session()->flash('success', 'Demande d\'absence enregistrée avec succès.');
    }


    public function getDemandeAbsence()
    {
        if (Auth::user()->employe->est_gestionnaire) {
            return DemandeAbsence::with(['employe', 'codeTravail'])
                ->where(function ($query) {
                    $query->whereHas('employe', function ($q) {
                        $q->where('gestionnaire_id', Auth::user()->employe->id);
                    })
                        ->orWhere('employe_id', Auth::user()->employe->id);
                })
                ->whereDate('date_fin', '>=', \Carbon\Carbon::today())
                ->paginate(10);
        }

        return DemandeAbsence::with(['employe', 'codeTravail'])
            ->where('employe_id', Auth::user()->employe->id)
            ->whereDate('date_fin', '>=', \Carbon\Carbon::today())
            ->paginate(10);
    }

    public function getDemandeAbsenceClose()
    {
        if (Auth::user()->employe->est_gestionnaire) {
            return DemandeAbsence::with(['employe', 'codeTravail'])
                ->where(function ($query) {
                    $query->whereHas('employe', function ($q) {
                        $q->where('gestionnaire_id', Auth::user()->employe->id);
                    })
                        ->orWhere('employe_id', Auth::user()->employe->id);
                })
                ->whereDate('date_fin', '<', \Carbon\Carbon::today())
                ->paginate(10);
        }

        return DemandeAbsence::with(['employe', 'codeTravail'])
            ->where('employe_id', Auth::user()->employe->id)
            ->whereDate('date_fin', '<', \Carbon\Carbon::today())
            ->paginate(10);
    }

    public function render()
    {
        return view(
            'rhfeuilledetempsabsence::livewire.rh-feuille-de-temps-absence-list',
            [
                'demande_absences' => $this->getDemandeAbsence(),
                'demande_absences_close' => $this->getDemandeAbsenceClose()

            ]
        );
    }
}
