<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;

class CodesTravailList extends Component
{
    use WithPagination;

    public $searchCode = '';
    public $searchLibelle = '';
    public $filterCategorie = '';
    public $showModal = false;
    public $editingId = null;
    public $showDetail = false;
    public $detailCodeTravailId = null;
    public $isFiltering = false;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'codeTravailCreated' => 'handleCodeTravailCreated',
        'codeTravailUpdated' => 'handleCodeTravailUpdated',
        'refreshComponent' => '$refresh'
    ];

    public function updatingSearchCode()
    {
        $this->resetPage();
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
        $this->detailCodeTravailId = $id;
        $this->showDetail = true;
    }

    public function closeDetailModal()
    {
        $this->showDetail = false;
        $this->detailCodeTravailId = null;
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
        
        $this->searchCode = '';
        $this->searchLibelle = '';
        $this->filterCategorie = '';
        $this->resetPage();
        $this->isFiltering = false;
    }

    public function handleCodeTravailCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Code de travail créé avec succès.');
    }

    public function handleCodeTravailUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Code de travail modifié avec succès.');
    }

    public function getCodesTravailProperty()
    {
        return CodeTravail::with('categorie')
            ->searchByCode($this->searchCode)
            ->searchByLibelle($this->searchLibelle)
            ->byCategorie($this->filterCategorie ?: null)
            ->orderBy('code')
            ->paginate(10);
    }

    public function getDetailCodeTravailProperty()
    {
        if ($this->detailCodeTravailId) {
            return CodeTravail::with('categorie')->find($this->detailCodeTravailId);
        }
        return null;
    }

    public function getCategoriesProperty()
    {
        return Categorie::orderBy('intitule')->get();
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.codes-travail-list', [
            'codesTravail' => $this->codesTravail,
            'detailCodeTravail' => $this->detailCodeTravail,
            'categories' => $this->categories
        ]);
    }
}
