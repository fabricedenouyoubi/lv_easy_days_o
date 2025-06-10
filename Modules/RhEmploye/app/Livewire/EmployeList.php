<?php

namespace Modules\RhEmploye\Livewire;

use Livewire\Component;
use Modules\RhEmploye\Models\Employe;
use Livewire\WithPagination;

class EmployeList extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingId = null;
    public $nom_searched;
    public $prenom_searched;
    public $matricule_searched;
    public $gestionnaire_searched;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'showModal' => 'closeModal',
        'employeCreated' => 'handleEmployeCreated',
    ];


    public function showCreateModal()
    {
        $this->editingId = null;
        $this->showModal = true;
    }

    public function closeModal($val = null)
    {
        $val ? $this->showModal = $this->val : $this->showModal = false;
        $this->editingId = null;
    }

    public function handleEmployeCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Employe crée avec succès.');
    }


    public function resetFilter()
    {
        $this->reset(['matricule_searched', 'nom_searched', 'prenom_searched', 'gestionnaire_searched']);
    }

    public function getEmployes()
    {
        return Employe::query()
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
                        $managerQuery->where('nom', 'like', '%' . $this->gestionnaire_searched . '%' );
                    });
                });
            })
            ->paginate(10);
    }

    public function render()
    {
        return view(
            'rhemploye::livewire.employe-list',
            ['employes' => $this->getEmployes()]
        );
    }
}
