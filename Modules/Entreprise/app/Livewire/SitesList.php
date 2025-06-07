<?php

namespace Modules\Entreprise\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Entreprise\Models\Site;

class SitesList extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $confirmingDelete = false;
    public $deletingId = null;
    public $showDetail = false;
    public $detailSiteId = null;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'siteCreated' => 'handleSiteCreated',
        'siteUpdated' => 'handleSiteUpdated',
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
        $this->detailSiteId = $id;
        $this->showDetail = true;
    }

    public function closeDetailModal()
    {
        $this->showDetail = false;
        $this->detailSiteId = null;
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->confirmingDelete = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->deletingId = null;
    }

    public function delete()
    {
        try {
            $site = Site::findOrFail($this->deletingId);
            
            // Supprimer l'adresse associée si elle existe
            if ($site->adresse) {
                $site->adresse->delete();
            }
            
            $site->delete();
            
            session()->flash('success', 'Site supprimé avec succès.');
            $this->cancelDelete();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            $this->cancelDelete();
        }
    }

    public function handleSiteCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Site créé avec succès.');
    }

    public function handleSiteUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Site modifié avec succès.');
    }

    public function getSitesProperty()
    {
        return Site::with('adresse')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);
    }

    public function getDetailSiteProperty()
    {
        if ($this->detailSiteId) {
            return Site::with('adresse', 'entreprise')->find($this->detailSiteId);
        }
        return null;
    }

    public function render()
    {
        return view('entreprise::livewire.sites-list', [
            'sites' => $this->sites,
            'detailSite' => $this->detailSite
        ]);
    }
}
