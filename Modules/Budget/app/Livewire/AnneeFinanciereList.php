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
    public $showModal = false;
    public $editingId = null;
    // Pour afficher un spinner pendant la génération
    public $generatingId = null; 

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'anneeFinanciereCreated' => 'handleAnneeFinanciereCreated',
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

    
    public function voirFeuillesDeTemps($anneeId)
    {
        try {
            $this->generatingId = $anneeId;
            
            $anneeFinanciere = AnneeFinanciere::findOrFail($anneeId);
            
            // Vérifier si le service de génération est disponible
            if (class_exists('Modules\RhFeuilleDeTempsConfig\Services\FeuilleDeTempsGeneratorService')) {
                
                // Vérifier si les feuilles existent déjà dans la base de données
                $existingFeuilles = \Modules\RhFeuilleDeTempsConfig\Models\FeuilleDeTemps::where('annee_financiere_id', $anneeFinanciere->id)->count();
                
                if ($existingFeuilles == 0) {
                    // Aucune feuille n'existe, on génère
                    $feuilleGenerator = app('Modules\RhFeuilleDeTempsConfig\Services\FeuilleDeTempsGeneratorService');
                    $jourFerieGenerator = app('Modules\RhFeuilleDeTempsConfig\Services\JourFerieGeneratorService');
                    
                    // Générer les jours fériés d'abord
                    $jourFerieGenerator->generateJourFerie($anneeFinanciere);
                    
                    // Puis générer les feuilles de temps
                    $feuilleGenerator->generateFeuillesDeTemps($anneeFinanciere);
                    
                    session()->flash('success', 'Les semaines générées pour l\'année ' . $anneeFinanciere->libelle);
                }
                
            }
            // Masquer le spinner
            $this->generatingId = null; 
            
            // Rediriger vers la page des détails dans tous les cas
            return redirect()->route('budget.annee-details', ['annee' => $anneeId]);
            
        } catch (\Exception $e) {
            $this->generatingId = null;
            session()->flash('error', 'Erreur lors de la vérification/génération : ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
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

    public function handleAnneeFinanciereCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Année financière créée avec succès.');
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