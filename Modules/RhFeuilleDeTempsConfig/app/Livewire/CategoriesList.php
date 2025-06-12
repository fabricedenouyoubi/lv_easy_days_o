<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;

class CategoriesList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterConfigurable = '';
    public $showModal = false;
    public $editingId = null;
    public $showDetail = false;
    public $detailCategorieId = null;
    public $isFiltering = false;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'categorieCreated' => 'handleCategorieCreated',
        'categorieUpdated' => 'handleCategorieUpdated',
        'refreshComponent' => '$refresh'
    ];

    public function updatingSearch()
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
        $this->detailCategorieId = $id;
        $this->showDetail = true;
    }

    public function closeDetailModal()
    {
        $this->showDetail = false;
        $this->detailCategorieId = null;
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
        
        $this->search = '';
        $this->filterConfigurable = '';
        $this->resetPage();
        $this->isFiltering = false;
    }

    public function handleCategorieCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Catégorie créée avec succès.');
    }

    public function handleCategorieUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Catégorie modifiée avec succès.');
    }

    public function getCategoriesProperty()
    {
        return Categorie::search($this->search)
            ->configurable($this->filterConfigurable ?: null)
            ->orderBy('intitule')
            ->paginate(10);
    }

    public function getDetailCategorieProperty()
    {
        if ($this->detailCategorieId) {
            return Categorie::find($this->detailCategorieId);
        }
        return null;
    }

    public function getFilterOptionsProperty()
    {
        return [
            '' => 'Tous',
            'aucun' => 'Non configurables',
            'Individuel' => 'Individuel',
            'Collectif' => 'Collectif',
            'Jour' => 'Jour',
        ];
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.categories-list', [
            'categories' => $this->categories,
            'detailCategorie' => $this->detailCategorie,
            'filterOptions' => $this->filterOptions
        ]);
    }
}
