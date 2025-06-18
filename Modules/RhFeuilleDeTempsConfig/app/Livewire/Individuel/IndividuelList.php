<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire\Individuel;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;


class IndividuelList extends Component
{
    use WithPagination;

    public $codeTravailId;
    public $codeTravail;
    public $searchEmploye = '';
    public $isFiltering = false;
    public $showModal = false;
    public $editingId = null;
    public $showDetail = false;
    public $detailConfigurationId = null;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'configurationCreated' => 'handleConfigurationCreated',
        'configurationUpdated' => 'handleConfigurationUpdated',
        'refreshComponent' => '$refresh'
    ];

    public function mount($codeTravailId)
    {
        $this->codeTravailId = $codeTravailId;
        $this->codeTravail = CodeTravail::with('categorie')->findOrFail($codeTravailId);
    }

    public function updatingSearchEmploye()
    {
        $this->resetPage();
    }

    public function showCreateModal()
    {
        $this->editingId = null;
        $this->showModal = true;
    }

    public function showEditModal($id)
    {
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function showDetailModal($id)
    {
        $this->detailConfigurationId = $id;
        $this->showDetail = true;
    }

    public function closeDetailModal()
    {
        $this->showDetail = false;
        $this->detailConfigurationId = null;
    }

    public function handleConfigurationCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Configuration employé ajoutée avec succès.');
    }

    public function handleConfigurationUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Configuration employé modifiée avec succès.');
    }

    public function filter()
    {
        $this->isFiltering = true;
        sleep(1);
        $this->resetPage();
        $this->isFiltering = false;
    }

    public function resetFilters()
    {
        $this->isFiltering = true;
        sleep(1);
        $this->searchEmploye = '';
        $this->resetPage();
        $this->isFiltering = false;
    }

    public function getConfigurationsProperty()
    {
        $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
        
        if (!$anneeBudgetaire) {
            return collect();
        }

        return Configuration::with(['employe'])
            ->where('code_travail_id', $this->codeTravailId)
            ->where('annee_budgetaire_id', $anneeBudgetaire->id)
            ->whereNotNull('employe_id') // Configuration individuelle
            ->when($this->searchEmploye, function ($query) {
                $query->whereHas('employe', function ($q) {
                    $q->where('nom', 'like', '%' . $this->searchEmploye . '%')
                      ->orWhere('prenom', 'like', '%' . $this->searchEmploye . '%')
                      ->orWhere('matricule', 'like', '%' . $this->searchEmploye . '%');
                });
            })
            ->orderBy('libelle')
            ->paginate(10);
    }

    public function getDetailConfigurationProperty()
    {
        if ($this->detailConfigurationId) {
            return Configuration::with(['employe', 'codeTravail', 'anneeBudgetaire'])->find($this->detailConfigurationId);
        }
        return null;
    }

    public function getAnneeBudgetaireActiveProperty()
    {
        return AnneeFinanciere::where('actif', true)->first();
    }

    public function getTitleModalProperty()
    {
        return "Liste des " . strtolower($this->codeTravail->libelle);
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.individuel.individuel-list', [
            'configurations' => $this->configurations,
            'detailConfiguration' => $this->detailConfiguration,
            'anneeBudgetaireActive' => $this->anneeBudgetaireActive,
            'titleModal' => $this->titleModal
        ]);
    }
}
