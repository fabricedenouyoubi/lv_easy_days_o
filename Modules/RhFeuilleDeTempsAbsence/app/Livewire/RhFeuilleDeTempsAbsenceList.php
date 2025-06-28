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
    public $nbrDemandeEnAttente;
    public $nbrDemandeApprouve;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'demandeAbsenceAjoute' => 'demandeAbsenceAjoute',
    ];


    public function mount()
    {
        if (Auth::user()->employe->est_gestionnaire) {
            //--- Nombre d'absences  approuvée et en attente affichées si un gestionnaire est connecté
            //--- en attente
            $this->nbrDemandeEnAttente = DemandeAbsence::gestionnaireConnecte()
                ->EnAttente()->count();

            //--- approuvée
            $this->nbrDemandeApprouve = DemandeAbsence::gestionnaireConnecte()
                ->approuve()->count();
        } else {
            //--- Nombre d'absences  approuvée et en attente affichées si un employé est connecté
            //--- en attente
            $this->nbrDemandeEnAttente = DemandeAbsence::employeConnecte()
                ->EnAttente()->count();

            //--- approuvée
            $this->nbrDemandeApprouve = DemandeAbsence::employeConnecte()
                ->approuve()->count();
        }
    }

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

    //--- recuperation des demandes d'absence en cours
    public function getDemandeAbsence()
    {

        if (Auth::user()->employe->est_gestionnaire) {
            return DemandeAbsence::with(['employe', 'codeTravail', 'operations.anneeSemaine'])
                ->gestionnaireConnecte()
                ->whereDate('date_fin', '>=', \Carbon\Carbon::today())
                ->paginate(10);
        }

        return DemandeAbsence::with(['employe', 'codeTravail', 'operations.anneeSemaine'])
            ->employeConnecte()
            ->whereDate('date_fin', '>=', \Carbon\Carbon::today())
            ->paginate(10);
    }

    //--- recuperation des demandes d'absence cloturées
    public function getDemandeAbsenceClose()
    {
        //--- absences affichées si un gestionnaire est connecté
        if (Auth::user()->employe->est_gestionnaire) {
            return DemandeAbsence::with(['employe', 'codeTravail', 'operations.anneeSemaine'])
                ->gestionnaireConnecte()
                ->whereDate('date_fin', '<', \Carbon\Carbon::today())
                ->paginate(10);
        }

        //--- absences affichées si un employé est connecté
        return DemandeAbsence::with(['employe', 'codeTravail', 'operations.anneeSemaine'])
            ->employeConnecte()
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
