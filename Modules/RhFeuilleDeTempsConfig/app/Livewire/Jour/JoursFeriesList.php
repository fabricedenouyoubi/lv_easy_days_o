<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire\Jour;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class JoursFeriesList extends Component
{

    use WithPagination;

    public $codeTravailId;
    public $codeTravail;
    public $searchLibelle = '';
    public $isFiltering = false;
    public $showModal = false;
    public $editingId = null;
    public $showDetail = false;
    public $detailJourFerieId = null;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'jourFerieCreated' => 'handleJourFerieCreated',
        'jourFerieUpdated' => 'handleJourFerieUpdated',
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
        $this->detailJourFerieId = $id;
        $this->showDetail = true;
    }

    public function closeDetailModal()
    {
        $this->showDetail = false;
        $this->detailJourFerieId = null;
    }

    public function handleJourFerieCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Jour férié ajouté avec succès.');
    }

    public function handleJourFerieUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Jour férié modifié avec succès.');
    }

    public function getJoursFeriesProperty()
    {
        $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
        
        if (!$anneeBudgetaire) {
            return collect();
        }

        return Configuration::joursFeries()
            ->forCodeTravail($this->codeTravailId)
            ->forAnnee($anneeBudgetaire->id)
            ->searchByLibelle($this->searchLibelle)
            ->orderBy('date')
            ->paginate(10);
    }
    public function filter()
    {
        $this->isFiltering = true;
        
        // Simuler un délai pour montrer le spinner
        sleep(1);
        
        $this->resetPage();
        $this->isFiltering = false;
    }

    public function resetFilters()
    {
        $this->isFiltering = true;
        
        // Simuler un délai pour montrer le spinner
        sleep(1);
        $this->searchLibelle = '';
        $this->resetPage();
        $this->isFiltering = false;
    }
    public function getDetailJourFerieProperty()
    {
        if ($this->detailJourFerieId) {
            return Configuration::with(['codeTravail', 'anneeBudgetaire'])->find($this->detailJourFerieId);
        }
        return null;
    }

    public function getAnneeBudgetaireActiveProperty()
    {
        return AnneeFinanciere::where('actif', true)->first();
    }

    public function getTitleModalProperty()
    {
        return strtolower($this->codeTravail->libelle);
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.jour.jours-feries-list', [
            'joursFeries' => $this->joursFeries,
            'detailJourFerie' => $this->detailJourFerie,
            'anneeBudgetaireActive' => $this->anneeBudgetaireActive,
            'titleModal' => $this->titleModal
        ]);
    }
    
}
