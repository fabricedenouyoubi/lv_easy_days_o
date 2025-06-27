<?php

namespace Modules\Rh\Livewire\Employe;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Rh\Models\Employe\Employe;

class EmployeList extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingId = null;
    public $employeId = null;
    public $employeNom;
    public $nom_searched;
    public $prenom_searched;
    public $matricule_searched;
    public $gestionnaire_searched;
    public $showAddEmployeAbsenceModal = false;

    protected $paginationTheme = 'bootstrap';

    //--- ecouteur d'evenement venamt des composants enfants [employe-form]
    protected $listeners = [
        'showModal' => 'closeModal',
        'employeCreated' => 'handleEmployeCreated',
        'demandeAbsenceAjoute' => 'demandeAbsenceAjoute',
    ];

    //--- fonction d'affichage du formulaire d'un employe
    public function showCreateModal()
    {
        $this->editingId = null;
        $this->showModal = true;
    }

    //--- fonction de fermeture du formulaire d'un employe
    public function closeModal($val = null)
    {

        $val ? $this->showModal = $this->val : $this->showModal = false;
        $this->editingId = null;
    }

    //--- afficher et caher le formulaire d'ajout d'une absence d'un employé
    public function open_add_employe_absence_modal($empId, $empNom)
    {
        $this->employeId = $empId;
        $this->employeNom = $empNom;
        $this->showAddEmployeAbsenceModal = !$this->showAddEmployeAbsenceModal;
    }

    public function close_add_employe_absence_modal()
    {
        $this->reset('employeId', 'employeNom');
        $this->showAddEmployeAbsenceModal = !$this->showAddEmployeAbsenceModal;
    }

    //--- afficher le message de creation d'une absence
    public function demandeAbsenceAjoute()
    {
        $this->showAddEmployeAbsenceModal = false;
        session()->flash('success', 'Demande d\'absence enregistrée avec succès.');
    }


    //--- fonction d'affichage du message de creation d'un employe
    public function handleEmployeCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Employe crée avec succès.');
    }

    //--- reinitialisation des champs du formulaire d'ajout d'um employe
    public function resetFilters()
    {
        $this->reset(['matricule_searched', 'nom_searched', 'prenom_searched', 'gestionnaire_searched']);
    }

    //-- recuperation de la liste des employes
    public function getEmployes()
    {
        return Employe::query()
            ->with('gestionnaire')
            ->when(
                $this->matricule_searched,
                fn($query) =>
                $query->where('matricule', 'like', '%' . $this->matricule_searched . '%')
            )
            ->when(
                $this->nom_searched,
                fn($query) =>
                $query->where('nom', 'like', '%' . $this->nom_searched . '%')
            )
            ->when(
                $this->prenom_searched,
                fn($query) =>
                $query->where('prenom', 'like', '%' . $this->prenom_searched . '%')
            )
            ->when($this->gestionnaire_searched, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereHas('gestionnaire', function ($managerQuery) {
                        $managerQuery->where('nom', 'like', '%' . $this->gestionnaire_searched . '%');
                    });
                });
            })
            ->orderBy('nom', 'asc')
            ->paginate(10);
    }

    public function render()
    {
        return view(
            'rh::livewire.employe.employe-list',
            ['employes' => $this->getEmployes()]
        );
    }
}
