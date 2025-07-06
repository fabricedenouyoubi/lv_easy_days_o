<?php

namespace Modules\RhFeuilleDeTempsReguliere\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class RhFeuilleDeTempsReguliereShow extends Component
{
    public $operationId;
    public $semaineId;
    public $operation;
    public $semaine;
    public $employe;
    public $lignesTravail = [];
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
    // Banque de temps
    public $banqueDeTemps = [];

    public $datesSemaine = [];

    public $totauxrecapitulatif = [];
    public $totalGeneral = 0;
    public $heureSupplementaireAjuste = 0;
    public $heureSupplementaireAPayer = 0;

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
            
            $this->employe = $this->operation->employe;
            
            // Vérifier les permissions d'accès
            $this->verifierPermissions();
            
            // Charger les lignes de travail
            $this->chargerLignesTravail();
            
            // Charger l'historique workflow
            $this->chargerWorkflowHistory();

            // Calculer la banque de temps
            $this->calculerBanqueDeTemps();

            // Calculer le récapitulatif dynamique
            $this->calculerRecapitulatif();

            // Calculer semaines
            $this->calculerDatesSemaine();
            
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors du chargement: ' . $th->getMessage());
        }
    }

    /**
     * Vérifier les permissions de l'utilisateur
     */
    private function verifierPermissions()
    {
        $user = Auth::user();
        $isOwner = $this->operation->employe_id === $user->employe->id;
        $isDirectManager = $this->operation->employe->gestionnaire_id === $user->employe->id;
        $isAdmin = $user->hasRole('ADMIN');

        // Vérifier l'accès en lecture
        if (!$isOwner && !$isDirectManager && !$isAdmin) {
            abort(403, 'Accès non autorisé à cette feuille de temps');
        }

        // Définir les permissions d'action selon les nouvelles règles
        $this->canEdit = $isOwner && $this->operation->canTransition('enregistrer');
        $this->canSubmit = $isOwner && $this->operation->canTransition('soumettre');
        $this->canRecall = $isOwner && $this->operation->canTransition('rappeler');
        
        // NOUVELLE RÈGLE: Seul le gestionnaire direct peut valider/rejeter
        // L'admin ne peut plus valider/rejeter sauf s'il est le gestionnaire direct
        $this->canApprove = $isDirectManager && $this->operation->canTransition('valider');
        $this->canReject = $isDirectManager && $this->operation->canTransition('rejeter');
        
        // NOUVELLE RÈGLE: Pour le rappel d'une feuille validée, seul l'admin peut le faire
        $currentState = $this->operation->getCurrentState();
        if ($currentState === 'valide') {
            $this->canReturn = $isAdmin && $this->operation->canTransition('retourner');
        } else {
            // Pour les autres états, admin uniquement
            $this->canReturn = $isAdmin && $this->operation->canTransition('retourner');
        }
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

    /**
     * Obtenir le statut formaté
     */
    public function getStatutFormate()
    {
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
     * Calculer la banque de temps dynamique
     */
    private function calculerBanqueDeTemps()
    {
        $banqueTemps = [];

        // Récupérer l'année financière active
        $anneeFinanciere = AnneeFinanciere::where('actif', true)->first();

        if (!$anneeFinanciere || !$this->employe) {
            $this->banqueDeTemps = [];
            return;
        }

        // Définir les codes à rechercher pour la banque de temps
        $codesRecherches = [
            'vacances' => ['VAC', 'VACATION', 'VACANCE', 'CONGE'],
            'banque_temps' => ['CAISS', 'BANQUE', 'BANK', 'BT'],
            'heure_csn' => ['CSN', 'HCSN', 'CSN_H']
        ];

        foreach ($codesRecherches as $type => $patterns) {
            $configuration = $this->rechercherConfigurationParCode($patterns, $anneeFinanciere->id);

            if ($configuration) {
                $banqueTemps[] = [
                    'type' => $type,
                    'libelle' => $this->getLibelleBanqueTemps($type, $configuration->codeTravail->libelle),
                    'valeur' => $configuration->reste ?? 0,
                    'code_travail' => $configuration->codeTravail
                ];
            }
        }

        $this->banqueDeTemps = $banqueTemps;
    }


    /**
     * Version corrigée - reste collectif partagé
     */
    private function rechercherConfigurationParCode($patterns, $anneeBudgetaireId)
    {
        // Recherche individuelle
        $configIndividuelle = Configuration::with('codeTravail')
            ->where('employe_id', $this->employe->id)
            ->where('annee_budgetaire_id', $anneeBudgetaireId)
            ->whereHas('codeTravail', function ($query) use ($patterns) {
                $query->where(function ($subQuery) use ($patterns) {
                    foreach ($patterns as $pattern) {
                        $subQuery->orWhere('code', 'LIKE', "%{$pattern}%")
                            ->orWhere('libelle', 'LIKE', "%{$pattern}%");
                    }
                });
            })
            ->first();

        if ($configIndividuelle) {
            return $configIndividuelle;
        }

        // Recherche collective avec reste partagé
        $configCollective = Configuration::with(['codeTravail', 'employes'])
            ->whereNull('employe_id')
            ->whereNull('date')
            ->where('annee_budgetaire_id', $anneeBudgetaireId)
            ->whereHas('codeTravail', function ($query) use ($patterns) {
                $query->where(function ($subQuery) use ($patterns) {
                    foreach ($patterns as $pattern) {
                        $subQuery->orWhere('code', 'LIKE', "%{$pattern}%")
                            ->orWhere('libelle', 'LIKE', "%{$pattern}%");
                    }
                });
            })
            ->whereHas('employes', function ($query) {
                $query->where('employe_id', $this->employe->id);
            })
            ->first();

        if ($configCollective) {
            // Retourner la configuration avec son reste
            return $configCollective;
        }

        return null;
    }

    /**
     * Obtenir le libellé formaté pour la banque de temps
     */
    private function getLibelleBanqueTemps($type, $libelleBrut)
    {
        return match ($type) {
            'vacances' => 'Vacances',
            'banque_temps' => 'Banque de temps',
            'heure_csn' => 'Heure CSN',
            default => $libelleBrut
        };
    }

    /**
     * Calculer le total de la banque de temps
     */
    public function getTotalBanqueTempsProperty()
    {
        return collect($this->banqueDeTemps)->sum('valeur');
    }

    /**
 * Calculer le récapitulatif dynamique basé sur les lignes saisies
 */
private function calculerRecapitulatif()
{
    $recapitulatif = [];
    $totalGeneral = 0;

    foreach ($this->lignesTravail as $ligne) {
        $codeTravail = $ligne['code_travail'];
        
        if ($ligne['total'] > 0) {
            $recapitulatif[] = [
                'code_travail' => $codeTravail,
                'total_heures' => $ligne['total']
            ];
            
            $totalGeneral += $ligne['total'];
        }
    }

    // Charger les heures supplémentaires depuis l'opération
    $this->heureSupplementaireAjuste = $this->operation->total_heure_supp_ajuster ?? 0;
    $this->heureSupplementaireAPayer = $this->operation->total_heure_sup_a_payer ?? 0;
    
    // Ajouter les heures supplémentaires au total
    $totalGeneral += $this->heureSupplementaireAjuste + $this->heureSupplementaireAPayer;

    // Trier par libellé
    usort($recapitulatif, function($a, $b) {
        return strcmp($a['code_travail']->libelle, $b['code_travail']->libelle);
    });

    $this->totauxrecapitulatif = $recapitulatif;
    $this->totalGeneral = $totalGeneral;
}
/**
 * Calculer les dates de la semaine pour affichage
 */
private function calculerDatesSemaine()
{
    $dateDebut = \Carbon\Carbon::parse($this->semaine->debut);

    for ($i = 0; $i <= 6; $i++) {
        $date = $dateDebut->copy()->addDays($i);
        $this->datesSemaine[] = [
            'date' => $date,
            'format' => $date->format('d') . ' ' . $date->locale('fr')->monthName . ' ' . $date->format('Y'),
            'is_dimanche' => $date->isSunday(),
            'jour_nom' => $date->locale('fr')->dayName,
            'jour_court' => $date->locale('fr')->shortDayName
        ];
    }
}
    public function render()
    {
        return view('rhfeuilledetempsreguliere::livewire.rh-feuille-de-temps-reguliere-show');
    }
}