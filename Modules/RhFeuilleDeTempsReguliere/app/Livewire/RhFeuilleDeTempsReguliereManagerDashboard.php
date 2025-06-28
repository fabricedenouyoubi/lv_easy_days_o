<?php

namespace Modules\RhFeuilleDeTempsReguliere\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;

class RhFeuilleDeTempsReguliereManagerDashboard extends Component
{
    use WithPagination;

    public $activeTab = 'feuilles';
    public $stats = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->calculerStatistiques();
    }

    /**
     * Calculer les statistiques du dashboard
     */
    private function calculerStatistiques()
    {
        $managerId = Auth::user()->employe->id;
        
        $this->stats = [
            // Feuilles de temps
            'feuilles_en_attente' => Operation::whereHas('employe', function($query) use ($managerId) {
                $query->where('gestionnaire_id', $managerId);
            })->where('workflow_state', 'soumis')->count(),
            
            'feuilles_validees_semaine' => Operation::whereHas('employe', function($query) use ($managerId) {
                $query->where('gestionnaire_id', $managerId);
            })->where('workflow_state', 'valide')
              ->where('updated_at', '>=', now()->startOfWeek())->count(),
            
            // Demandes d'absence
            'absences_en_attente' => DemandeAbsence::whereHas('employe', function($query) use ($managerId) {
                $query->where('gestionnaire_id', $managerId);
            })->where('statut', 'Soumis')->count(),
            
            'absences_validees_mois' => DemandeAbsence::whereHas('employe', function($query) use ($managerId) {
                $query->where('gestionnaire_id', $managerId);
            })->where('statut', 'Validé')
              ->where('updated_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    /**
     * Obtenir les feuilles de temps en attente
     */
    public function getFeuillesEnAttente()
    {
        $managerId = Auth::user()->employe->id;
        
        return Operation::with(['employe', 'anneeSemaine'])
            ->whereHas('employe', function($query) use ($managerId) {
                $query->where('gestionnaire_id', $managerId);
            })
            ->where('workflow_state', 'soumis')
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'feuilles_page');
    }

    /**
     * Obtenir les demandes d'absence en attente
     */
    public function getAbsencesEnAttente()
    {
        $managerId = Auth::user()->employe->id;
        
        return DemandeAbsence::with(['employe', 'codeTravail'])
            ->whereHas('employe', function($query) use ($managerId) {
                $query->where('gestionnaire_id', $managerId);
            })
            ->where('statut', 'Soumis')
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'absences_page');
    }

    /**
     * Changer d'onglet
     */
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    /**
     * Actions rapides de validation
     */
    public function approuverFeuille($operationId)
    {
        try {
            $operation = Operation::findOrFail($operationId);
            
            // Vérifier les permissions
            if ($operation->employe->gestionnaire_id !== Auth::user()->employe->id && !Auth::user()->hasRole('ADMIN')) {
                session()->flash('error', 'Permission refusée');
                return;
            }
            
            $operation->applyTransition('valider', [
                'comment' => 'Validation rapide par ' . Auth::user()->name
            ]);
            
            session()->flash('success', 'Feuille de temps validée avec succès');
            $this->calculerStatistiques();
            
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de la validation: ' . $th->getMessage());
        }
    }

    /**
     * Action rapide de validation d'absence
     */
    public function approuverAbsence($absenceId)
    {
        try {
            $absence = DemandeAbsence::findOrFail($absenceId);
            
            // Vérifier les permissions
            if ($absence->employe->gestionnaire_id !== Auth::user()->employe->id && !Auth::user()->hasRole('ADMIN')) {
                session()->flash('error', 'Permission refusée');
                return;
            }
            
            $absence->applyTransition('valider', [
                'comment' => 'Validation rapide par ' . Auth::user()->name
            ]);
            
            session()->flash('success', 'Demande d\'absence validée avec succès');
            $this->calculerStatistiques();
            
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de la validation: ' . $th->getMessage());
        }
    }

    public function render()
    {
        return view('rhfeuilledetempsreguliere::livewire.rh-feuille-de-temps-reguliere-manager-dashboard', [
            'feuilles_attente' => $this->getFeuillesEnAttente(),
            'absences_attente' => $this->getAbsencesEnAttente()
        ]);
    }
}
