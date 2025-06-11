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
    public $dateDebut = '';
    public $dateFin = '';
    public $showOnlyActive = false;
    
    // Variables pour stocker les filtres appliqués
    public $appliedDateDebut = '';
    public $appliedDateFin = '';
    public $appliedShowOnlyActive = false;
    
    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'refreshFeuillesList' => '$refresh'
    ];

    public function mount($anneeFinanciereId)
    {
        $this->anneeFinanciere = AnneeFinanciere::findOrFail($anneeFinanciereId);
    }

    public function applyFilters()
    {
        // Copier les valeurs des filtres vers les variables appliquées
        $this->appliedDateDebut = $this->dateDebut;
        $this->appliedDateFin = $this->dateFin;
        $this->appliedShowOnlyActive = $this->showOnlyActive;
        
        // Réinitialiser la pagination
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->dateDebut = '';
        $this->dateFin = '';
        $this->showOnlyActive = false;
        
        $this->appliedDateDebut = '';
        $this->appliedDateFin = '';
        $this->appliedShowOnlyActive = false;
        
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
            ->when($this->appliedDateDebut, function ($query) {
                $query->where('debut', '>=', $this->appliedDateDebut);
            })
            ->when($this->appliedDateFin, function ($query) {
                $query->where('fin', '<=', $this->appliedDateFin);
            })
            ->when($this->appliedShowOnlyActive, function ($query) {
                $query->where('actif', true);
            })
            ->orderBy('numero_semaine')
            ->paginate(20);
    }

    public function getStatistiquesProperty()
    {
        $query = FeuilleDeTemps::where('annee_financiere_id', $this->anneeFinanciere->id)
            ->when($this->appliedDateDebut, function ($query) {
                $query->where('debut', '>=', $this->appliedDateDebut);
            })
            ->when($this->appliedDateFin, function ($query) {
                $query->where('fin', '<=', $this->appliedDateFin);
            });

        return [
            'total' => $query->count(),
            'actives' => $query->clone()->where('actif', true)->count(),
            'inactives' => $query->clone()->where('actif', false)->count(),
            'semaines_paie' => $query->clone()->where('est_semaine_de_paie', true)->count(),
        ];
    }


    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.feuille-de-temps-details', [
            'feuilles' => $this->feuilles,
            'statistiques' => $this->statistiques
        ]);
    }
}
