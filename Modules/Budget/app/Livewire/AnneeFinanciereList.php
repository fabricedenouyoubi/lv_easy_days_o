<?php

namespace Modules\Budget\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\AnneeFinanciere;

class AnneeFinanciereList extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $confirmingDelete = false;
    public $deletingId = null;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'anneeFinanciereCreated' => 'handleAnneeFinanciereCreated',
        'anneeFinanciereUpdated' => 'handleAnneeFinanciereUpdated',
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
            $annee = AnneeFinanciere::findOrFail($this->deletingId);
            
            // Vérifier si c'est l'année active
            if ($annee->statut === AnneeFinanciere::STATUT_ACTIF) {
                session()->flash('error', 'Impossible de supprimer l\'année financière active.');
                $this->cancelDelete();
                return;
            }

            $annee->delete();
            
            session()->flash('success', 'Année financière supprimée avec succès.');
            $this->cancelDelete();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            $this->cancelDelete();
        }
    }

    public function activer($id)
    {
        try {
            $annee = AnneeFinanciere::findOrFail($id);
            $annee->activer();
            
            session()->flash('success', 'Année financière activée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'activation : ' . $e->getMessage());
        }
    }

    public function cloturerEtCreerSuivante($id)
    {
        try {
            $annee = AnneeFinanciere::findOrFail($id);
            $nouvelleAnnee = $annee->cloturerEtCreerSuivante();
            
            session()->flash('success', 'Année financière clôturée et nouvelle année créée : ' . $nouvelleAnnee->libelle);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la clôture : ' . $e->getMessage());
        }
    }

    public function handleAnneeFinanciereCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Année financière créée avec succès.');
    }

    public function handleAnneeFinanciereUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Année financière modifiée avec succès.');
    }

    public function getAnneesProperty()
    {
        return AnneeFinanciere::when($this->search, function ($query) {
                $query->whereYear('debut', 'like', '%' . $this->search . '%')
                      ->orWhereYear('fin', 'like', '%' . $this->search . '%');
            })
            ->orderBy('debut', 'desc')
            ->paginate(10);
    }
    public function render()
    {
        return view('budget::livewire.annee-financiere-list', [
            'annees' => $this->annees
        ]);
    }
}
