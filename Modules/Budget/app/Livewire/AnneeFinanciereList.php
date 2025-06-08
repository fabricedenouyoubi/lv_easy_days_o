<?php

namespace Modules\Budget\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\AnneeFinanciere;

class AnneeFinanciereList extends Component
{
    use WithPagination;

    public $search = '';
    public $showClotureModal = false;
    public $cloturingId = null;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'refreshComponent' => '$refresh'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function voirFeuillesDeTemps($anneeId)
    {
        // Rediriger vers la page des détails de l'année financière
        return redirect()->route('budget.annee-details', ['annee' => $anneeId]);
    }

    public function confirmCloture($id)
    {
        $this->cloturingId = $id;
        $this->showClotureModal = true;
    }

    public function cancelCloture()
    {
        $this->showClotureModal = false;
        $this->cloturingId = null;
    }

    public function cloturerEtCreerSuivante()
    {
        try {
            $annee = AnneeFinanciere::findOrFail($this->cloturingId);
            $nouvelleAnnee = $annee->cloturerEtCreerSuivante();
            
            session()->flash('success', 'Année financière clôturée et nouvelle année créée : ' . $nouvelleAnnee->libelle);
            $this->cancelCloture();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la clôture : ' . $e->getMessage());
            $this->cancelCloture();
        }
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
