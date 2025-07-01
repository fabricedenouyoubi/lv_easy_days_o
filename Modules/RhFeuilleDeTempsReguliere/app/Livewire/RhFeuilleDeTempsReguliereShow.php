<?php

namespace Modules\RhFeuilleDeTempsReguliere\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;

class RhFeuilleDeTempsReguliereShow extends Component
{
    public $operationId;
    public $semaineId;
    public $operation;
    public $semaine;
    public $lignesTravail = [];
    public $joursLabels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    public $workflowHistory = [];
    
    // Permissions utilisateur
    public $canEdit = false;
    public $canSubmit = false;
    public $canRecall = false;
    public $canApprove = false;
    public $canReject = false;
    public $canReturn = false;
    
    // Modal states
    public $showSubmitModal = false;
    public $showRecallModal = false;
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $showReturnModal = false;
    
    public $motifRejet = '';
    public $commentaire = '';

    protected $rules = [
        'motifRejet' => 'required|string|min:5',
        'commentaire' => 'nullable|string|max:500'
    ];

    public function mount()
    {
        try {
            $this->semaine = SemaineAnnee::findOrFail($this->semaineId);
            $this->operation = Operation::with(['lignesTravail.codeTravail', 'employe', 'anneeSemaine'])
                                      ->findOrFail($this->operationId);
            
            // Vérifier que l'opération et l'employé existent
            if (!$this->operation || !$this->operation->employe) {
                throw new \Exception('Opération ou employé non trouvé');
            }
            
            // Vérifier les permissions d'accès
            $this->verifierPermissions();
            
            // Charger les lignes de travail
            $this->chargerLignesTravail();
            
            // Charger l'historique workflow
            $this->chargerWorkflowHistory();
            
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors du chargement: ' . $th->getMessage());
            return redirect()->route('feuille-temps.list');
        }
    }

    /**
     * Computed property pour l'employé
     */
    #[Computed]
    public function employe()
    {
        return $this->operation?->employe;
    }

    /**
     * Computed property pour le statut formaté
     */
    #[Computed]
    public function statutFormate(): array
    {
        if (!$this->operation) {
            return [
                'text' => 'Aucune opération',
                'class' => 'bg-secondary',
                'icon' => 'fas fa-exclamation-triangle'
            ];
        }

        return match($this->operation->workflow_state) {
            'brouillon' => [
                'text' => 'Brouillon',
                'class' => 'bg-warning text-dark',
                'icon' => 'fas fa-pencil-alt'
            ],
            'en_cours' => [
                'text' => 'En cours',
                'class' => 'bg-info text-dark',
                'icon' => 'fas fa-hourglass-half'
            ],
            'soumis' => [
                'text' => 'Soumis',
                'class' => 'bg-primary',
                'icon' => 'fas fa-paper-plane'
            ],
            'valide' => [
                'text' => 'Validé',
                'class' => 'bg-success',
                'icon' => 'fas fa-check-circle'
            ],
            'rejete' => [
                'text' => 'Rejeté',
                'class' => 'bg-danger',
                'icon' => 'fas fa-times-circle'
            ],
            default => [
                'text' => 'Inconnu',
                'class' => 'bg-secondary',
                'icon' => 'fas fa-question-circle'
            ]
        };
    }

    /**
     * Vérifier les permissions de l'utilisateur
     */
    private function verifierPermissions()
    {
        $user = Auth::user();
        $isOwner = $this->operation->employe_id === $user->employe->id;
        $isManager = $this->operation->employe->gestionnaire_id === $user->employe->id;
        $isAdmin = $user->hasRole('ADMIN');

        // Vérifier l'accès en lecture
        if (!$isOwner && !$isManager && !$isAdmin) {
            abort(403, 'Accès non autorisé à cette feuille de temps');
        }

        // Définir les permissions d'action
        $this->canEdit = $isOwner && $this->operation->canTransition('enregistrer');
        $this->canSubmit = $isOwner && $this->operation->canTransition('soumettre');
        $this->canRecall = $isOwner && $this->operation->canTransition('rappeler');
        $this->canApprove = ($isManager || $isAdmin) && $this->operation->canTransition('valider');
        $this->canReject = ($isManager || $isAdmin) && $this->operation->canTransition('rejeter');
        $this->canReturn = $isAdmin && $this->operation->canTransition('retourner');
    }

    /**
     * Charger les lignes de travail
     */
    private function chargerLignesTravail()
    {
        $this->lignesTravail = $this->operation->lignesTravail->map(function($ligne) {
            return [
                'id' => $ligne->id,
                'code_travail' => $ligne->codeTravail,
                'jours' => $ligne->getJoursData(),
                'total' => $ligne->getTotalHeures(),
                'auto_rempli' => $ligne->auto_rempli ?? false,
                'type_auto_remplissage' => $ligne->type_auto_remplissage
            ];
        })->toArray();
    }

    /**
     * Charger l'historique du workflow
     */
    private function chargerWorkflowHistory()
    {
        $this->workflowHistory = $this->operation->getWorkflowHistory();
    }

    /**
     * Transitions workflow
     */
    public function toggleSubmitModal()
    {
        $this->showSubmitModal = !$this->showSubmitModal;
    }

    public function toggleRecallModal()
    {
        $this->showRecallModal = !$this->showRecallModal;
    }

    public function toggleApproveModal()
    {
        $this->showApproveModal = !$this->showApproveModal;
    }

    public function toggleRejectModal()
    {
        $this->showRejectModal = !$this->showRejectModal;
    }

    public function toggleReturnModal()
    {
        $this->showReturnModal = !$this->showReturnModal;
    }

    /**
     * Soumettre la feuille de temps
     */
    public function soumettre()
    {
        try {
            $this->operation->applyTransition('soumettre', [
                'comment' => $this->commentaire ?: 'Feuille de temps soumise'
            ]);
            
            $this->showSubmitModal = false;
            $this->commentaire = '';
            session()->flash('success', 'Feuille de temps soumise avec succès.');
            
            // Recharger pour mettre à jour les permissions
            $this->mount();
            
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de la soumission: ' . $th->getMessage());
        }
    }

    /**
     * Rappeler la feuille de temps
     */
    public function rappeler()
    {
        try {
            $this->operation->applyTransition('rappeler', [
                'comment' => $this->commentaire ?: 'Feuille de temps rappelée pour modification'
            ]);
            
            $this->showRecallModal = false;
            $this->commentaire = '';
            session()->flash('success', 'Feuille de temps rappelée avec succès.');
            
            $this->mount();
            
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors du rappel: ' . $th->getMessage());
        }
    }

    /**
     * Approuver la feuille de temps
     */
    public function approuver()
    {
        try {
            $this->operation->applyTransition('valider', [
                'comment' => $this->commentaire ?: 'Feuille de temps validée par ' . Auth::user()->name
            ]);
            
            $this->showApproveModal = false;
            $this->commentaire = '';
            session()->flash('success', 'Feuille de temps validée avec succès.');
            
            $this->mount();
            
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de la validation: ' . $th->getMessage());
        }
    }

    /**
     * Rejeter la feuille de temps
     */
    public function rejeter()
    {
        $this->validate(['motifRejet' => 'required|string|min:5']);
        
        try {
            $this->operation->applyTransition('rejeter', [
                'motif_rejet' => $this->motifRejet,
                'comment' => 'Feuille de temps rejetée par ' . Auth::user()->name . '. Motif: ' . $this->motifRejet
            ]);
            
            $this->showRejectModal = false;
            $this->motifRejet = '';
            session()->flash('success', 'Feuille de temps rejetée avec succès.');
            
            $this->mount();
            
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors du rejet: ' . $th->getMessage());
        }
    }

    /**
     * Retourner la feuille de temps (admin)
     */
    public function retourner()
    {
        $this->validate(['motifRejet' => 'required|string|min:5']);
        
        try {
            $this->operation->applyTransition('retourner', [
                'motif_rejet' => $this->motifRejet,
                'comment' => 'Feuille de temps retournée par ' . Auth::user()->name . '. Motif: ' . $this->motifRejet
            ]);
            
            $this->showReturnModal = false;
            $this->motifRejet = '';
            session()->flash('success', 'Feuille de temps retournée avec succès.');
            
            $this->mount();
            
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors du retour: ' . $th->getMessage());
        }
    }

    public function render()
    {
        return view('rhfeuilledetempsreguliere::livewire.rh-feuille-de-temps-reguliere-show');
    }
}