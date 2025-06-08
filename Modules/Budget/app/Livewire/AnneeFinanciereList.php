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
    public $generatingId = null; 
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
        try {
            $this->generatingId = $anneeId; // Afficher le spinner
            
            $anneeFinanciere = AnneeFinanciere::findOrFail($anneeId);
            
            // Vérifier si le service de génération est disponible
            if (class_exists('Modules\RhFeuilleDeTempsConfig\Services\FeuilleDeTempsGeneratorService')) {
                $feuilleGenerator = app('Modules\RhFeuilleDeTempsConfig\Services\FeuilleDeTempsGeneratorService');
                $jourFerieGenerator = app('Modules\RhFeuilleDeTempsConfig\Services\JourFerieGeneratorService');
                
                // Vérifier si les feuilles ont déjà été générées
                if (!$feuilleGenerator->areFeuillesGenerated($anneeFinanciere)) {
                    // Générer les jours fériés d'abord
                    $jourFerieGenerator->generateJourFerie($anneeFinanciere);
                    
                    // Puis générer les feuilles de temps
                    $feuilleGenerator->generateFeuillesDeTemps($anneeFinanciere);
                    
                    session()->flash('success', 'Feuilles de temps générées automatiquement pour l\'année ' . $anneeFinanciere->libelle);
                }
            }
            
            $this->generatingId = null; // Masquer le spinner
            
            // Rediriger vers la page des détails
            return redirect()->route('budget.annee-details', ['annee' => $anneeId]);
            
        } catch (\Exception $e) {
            $this->generatingId = null;
            session()->flash('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }
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
