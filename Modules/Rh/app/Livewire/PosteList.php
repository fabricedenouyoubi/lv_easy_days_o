<?php

namespace Modules\Rh\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Rh\Models\Poste;

class PosteList extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingId = null;
    public $deletingId;
    public $search_libelle = '';
    public $search_decription = '';
    public $confirmingDelete;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'showModal' => 'closeModal',
        'posteCreated' => 'handlePosteCreated',
        'posteUpdated' => 'handlePosteUpdated',
    ];


    public function showCreateModal()
    {
        $this->editingId = null;
        $this->showModal = true;
        $this->dispatch('show-poste-modal');
    }

    public function showEditModal($id)
    {
        $this->editingId = $id;
        $this->showModal = true;
        $this->dispatch('show-poste-modal');
    }

    public function closeModal($val = null)
    {
        $val ? $this->showModal = $this->val : $this->showModal = false;
        $this->editingId = null;
    }

    public function handlePosteCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Poste crée avec succès.');
    }

    public function handlePosteUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Poste modifié avec succès.');
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->deletingId = null;
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        try {
            $poste = Poste::findOrFail($this->deletingId);
            $poste->delete();
            session()->flash('success', 'Poste supprimée avec succès.');
            $this->cancelDelete();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            $this->cancelDelete();
        }
    }


    public function getAnneesProperty()
    {
        return Poste::query()
            ->when(
                $this->search_libelle,
                fn($query) =>
                $query->where('libelle', 'like', '%' . $this->search_libelle . '%')
            )
            ->when(
                $this->search_decription,
                fn($query) =>
                $query->orWhere('description', 'like', '%' . $this->search_decription . '%')
            )
            ->paginate(10);
    }

    public function render()
    {
        //dd(Poste::all());
        return view('rh::livewire.poste-list', [
            'postes' => $this->getAnneesProperty()
        ]);
    }
}
