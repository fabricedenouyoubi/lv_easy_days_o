<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire\Collectif;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class CollectifList extends Component
{
    use WithPagination;

    public $codeTravailId;
    public $codeTravail;
    public $searchLibelle = '';
    public $isFiltering = false;
    public $showModal = false;
    public $editingId = null;
    public $showDetail = false;
    public $detailConfigurationId = null;
    public $showAffectation = false;
    public $affectationConfigurationId = null;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'configurationCreated' => 'handleConfigurationCreated',
        'configurationUpdated' => 'handleConfigurationUpdated',
        'employesAffected' => 'handleEmployesAffected',
        'refreshComponent' => '$refresh'
    ];

    public function mount($codeTravailId)
    {
        $this->codeTravailId = $codeTravailId;
        $this->codeTravail = CodeTravail::with('categorie')->findOrFail($codeTravailId);
    }

    public function updatingSearchLibelle()
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

    public function showAffectationModal($id)
    {
        $this->affectationConfigurationId = $id;
        $this->showAffectation = true;
    }

    public function closeAffectationModal()
    {
        $this->showAffectation = false;
        $this->affectationConfigurationId = null;
    }

    public function handleConfigurationCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Configuration collective créée avec succès.');
    }

    public function handleConfigurationUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Configuration collective modifiée avec succès.');
    }

    public function handleEmployesAffected()
    {
        $this->closeAffectationModal();
        session()->flash('success', 'Employés affectés avec succès.');
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
        $this->searchLibelle = '';
        $this->resetPage();
        $this->isFiltering = false;
    }

    public function getConfigurationsProperty()
    {
        $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
        
        if (!$anneeBudgetaire) {
            return collect();
        }

        return Configuration::with(['employes'])
            ->collectif() // Scope pour configurations collectives
            ->where('code_travail_id', $this->codeTravailId)
            ->where('annee_budgetaire_id', $anneeBudgetaire->id)
            ->when($this->searchLibelle, function ($query) {
                $query->where('libelle', 'like', '%' . $this->searchLibelle . '%');
            })
            ->orderBy('libelle')
            ->paginate(10);
    }

    public function getDetailConfigurationProperty()
    {
        if ($this->detailConfigurationId) {
            return Configuration::with(['employes', 'codeTravail', 'anneeBudgetaire'])->find($this->detailConfigurationId);
        }
        return null;
    }

    public function getAffectationConfigurationProperty()
    {
        if ($this->affectationConfigurationId) {
            return Configuration::with(['employes'])->find($this->affectationConfigurationId);
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
        return view('rhfeuilledetempsconfig::livewire.collectif.collectif-list', [
            'configurations' => $this->configurations,
            'detailConfiguration' => $this->detailConfiguration,
            'affectationConfiguration' => $this->affectationConfiguration,
            'anneeBudgetaireActive' => $this->anneeBudgetaireActive,
            'titleModal' => $this->titleModal
        ]);
    }
}
