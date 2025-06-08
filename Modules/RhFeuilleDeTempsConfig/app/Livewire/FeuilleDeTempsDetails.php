<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Models\FeuilleDeTemps;

class FeuilleDeTempsDetails extends Component
{

    use WithPagination;

    public $anneeFinanciere;
    public $search = '';
    public $statusFilter = 'all';
    public $showOnlyActive = true;
    
    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'refreshFeuillesList' => '$refresh'
    ];

    public function mount($anneeFinanciereId)
    {
        $this->anneeFinanciere = AnneeFinanciere::findOrFail($anneeFinanciereId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingShowOnlyActive()
    {
        $this->resetPage();
    }

    public function toggleSemaineDePaie($feuilleId)
    {
        try {
            $feuille = FeuilleDeTemps::findOrFail($feuilleId);
            $feuille->update(['est_semaine_de_paie' => !$feuille->est_semaine_de_paie]);
            
            $status = $feuille->est_semaine_de_paie ? 'marquée' : 'non marquée';
            session()->flash('success', "Semaine {$feuille->numero_semaine} {$status} comme semaine de paie.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la modification : ' . $e->getMessage());
        }
    }

    public function activerFeuille($feuilleId)
    {
        try {
            $feuille = FeuilleDeTemps::findOrFail($feuilleId);
            $feuille->update(['actif' => true]);
            
            session()->flash('success', "Semaine {$feuille->numero_semaine} activée.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'activation : ' . $e->getMessage());
        }
    }

    public function desactiverFeuille($feuilleId)
    {
        try {
            $feuille = FeuilleDeTemps::findOrFail($feuilleId);
            $feuille->update(['actif' => false]);
            
            session()->flash('success', "Semaine {$feuille->numero_semaine} désactivée.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la désactivation : ' . $e->getMessage());
        }
    }

    public function getFeuillesProperty()
    {
        return FeuilleDeTemps::where('annee_financiere_id', $this->anneeFinanciere->id)
            ->when($this->search, function ($query) {
                $query->where('numero_semaine', 'like', '%' . $this->search . '%');
            })
            ->when($this->showOnlyActive, function ($query) {
                $query->where('actif', true);
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                if ($this->statusFilter === 'paie') {
                    $query->where('est_semaine_de_paie', true);
                } elseif ($this->statusFilter === 'normal') {
                    $query->where('est_semaine_de_paie', false);
                }
            })
            ->orderBy('numero_semaine')
            ->paginate(20);
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.feuille-de-temps-details', [
            'feuilles' => $this->feuilles
        ]);
    }
}
