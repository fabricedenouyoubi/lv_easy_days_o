<?php

namespace Modules\RhCodeTravailComportement\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhCodeTravailComportement\Models\Configuration;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;

class JoursFeriesList extends Component
{
    use WithPagination;

    public $codeTravailId;
    public $codeTravail;
    public $searchLibelle = '';
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

    public function render()
    {
        return view('rhcodetravailcomportement::livewire.jours-feries-list', [
            'joursFeries' => $this->joursFeries,
            'detailJourFerie' => $this->detailJourFerie,
            'anneeBudgetaireActive' => $this->anneeBudgetaireActive
        ]);
    }
}