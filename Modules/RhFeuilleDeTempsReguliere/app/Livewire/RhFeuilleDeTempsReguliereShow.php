<?php

namespace Modules\RhFeuilleDeTempsReguliere\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;
use Modules\RhFeuilleDeTempsReguliere\Workflows\FeuilleTempsWorkflow;
use Workflow\WorkflowStub;

class RhFeuilleDeTempsReguliereShow extends Component
{
    public $operationId;
    public $semaineId;
    public $operation;
    public $semaine;
    public $employe;
    public $lignesTravail = [];
    public $workflowHistory = [];

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
    // Heures que l'employé doit à l'entreprise
    public $heuresManquantes = 0;

    public $datesSemaine = [];

    public $totauxrecapitulatif = [];
    public $totalGeneral = 0;
    public $heureSupplementaireAjuste = 0;
    public $heureSupplementaireAPayer = 0;

    // Jours de la semaine avec dates complètes
    public $joursFeries = [];

    protected $rules = [
        'motifRejet' => 'required|string|min:5',
        'commentaire' => 'nullable|string|max:500'
    ];

    // Nouvelles propriétés pour les calculs d'heures supplémentaires
    public $heuresDefiniesEmploye = 0; // Heures hebdomadaires de l'employé
    public $heuresTravaillees = 0; // Total heures travaillées cette semaine
    public $heuresSupNormales = 0; // Heures sup. normales (≤ 40h)
    public $heuresSupMajorees = 0; // Heures sup. majorées (> 40h × 1.5)
    public $totalHeuresSupAjustees = 0; // Total heures sup. ajustées (calculé)
    public $versBanqueTemps = 0; // Heures qui vont en banque de temps
    public $ajustementBanque = 0; // Ajustement final de la banque

    // Données de debug
    public $debugCalculs = [];

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

            // Charger les heures définies pour l'employé
            $this->chargerHeuresDefiniesEmploye();

            // Charger l'historique workflow
            $this->chargerWorkflowHistory();

            // Calculer la banque de temps
            $this->calculerBanqueDeTemps();

            // Calculer le récapitulatif dynamique
            $this->calculerRecapitulatif();

            // Calculer semaines
            $this->calculerDatesSemaine();

            // Calculer les jours fériés
            $this->calculerJoursFeries();

            // Calculer les détails des heures supplémentaires
            $this->calculerDetailsHeuresSupplementaires();
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
    }

    /**
     * Charger les lignes de travail
     */
    private function chargerLignesTravail()
    {
        $this->lignesTravail = $this->operation->lignesTravail->map(function ($ligne) {
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
     * Transitions workflow avec WorkflowStub
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
        $user_connect = Auth::user();
        try {
            $comment = 'Feuille de temps soumise par ' . $user_connect->name;

            //--- Workflow ---
            $workflow = WorkflowStub::make(FeuilleTempsWorkflow::class);
            $workflow->start($this->operation, 'soumettre', ['comment' => $comment, 'user' => $user_connect->name]);

            while ($workflow->running());

            if ($workflow->failed()) {
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow de soumission de la feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                    ['operation' => $this->operation->id]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow de soumission.');
            } else {
                Log::channel('daily')->info("La feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " vient d'être soumise par l'utilisateur " . $user_connect->name, ['operation' => $this->operation->id]);
                session()->flash('success', 'Feuille de temps soumise avec succès.');
                $this->showSubmitModal = false;
                $this->commentaire = '';
                $this->mount();
            }
        } catch (\Throwable $th) {
            Log::channel('daily')->error(
                "Erreur lors de la soumission de la feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'operation' => $this->operation->id]
            );
            session()->flash('error', 'Une erreur est survenue lors de la soumission de la feuille de temps.');
        }
    }

    /**
     * Rappeler la feuille de temps
     */
    public function rappeler()
    {
        $user_connect = Auth::user();
        try {
            $comment = 'Feuille de temps rappelée par ' . $user_connect->name;

            //--- Workflow ---
            $workflow = WorkflowStub::make(FeuilleTempsWorkflow::class);
            $workflow->start($this->operation, 'rappeler', ['comment' => $comment, 'user' => $user_connect->name]);

            while ($workflow->running());

            if ($workflow->failed()) {
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow de rappel de la feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                    ['operation' => $this->operation->id]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow de rappel.');
            } else {
                Log::channel('daily')->info("La feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " vient d'être rappelée par l'utilisateur " . $user_connect->name, ['operation' => $this->operation->id]);
                session()->flash('success', 'Feuille de temps rappelée avec succès.');
                $this->showRecallModal = false;
                $this->commentaire = '';
                $this->mount();
            }
        } catch (\Throwable $th) {
            Log::channel('daily')->error(
                "Erreur lors du rappel de la feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'operation' => $this->operation->id]
            );
            session()->flash('error', 'Une erreur est survenue lors du rappel de la feuille de temps.');
        }
    }

    /**
     * Approuver la feuille de temps
     */
    public function approuver()
    {
        $user_connect = Auth::user();
        try {
            $comment = 'Feuille de temps validée par ' . $user_connect->name;

            //--- Workflow ---
            $workflow = WorkflowStub::make(FeuilleTempsWorkflow::class);
            $workflow->start($this->operation, 'valider', ['comment' => $comment, 'user' => $user_connect->name]);

            while ($workflow->running());

            if ($workflow->failed()) {
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow de validation de la feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                    ['operation' => $this->operation->id]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow de validation.');
            } else {
                // Mettre à jour la banque de temps avec "vers banque de temps"
                $this->mettreAJourBanqueTemps();

                // Traiter les codes déductibles
                $this->traiterCodesDeductiblesValidation();

                Log::channel('daily')->info("La feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " vient d'être validée par l'utilisateur " . $user_connect->name, ['operation' => $this->operation->id]);
                session()->flash('success', 'Feuille de temps validée avec succès.');
                $this->showApproveModal = false;
                $this->commentaire = '';
                $this->mount();
            }
        } catch (\Throwable $th) {
            Log::channel('daily')->error(
                "Erreur lors de la validation de la feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'operation' => $this->operation->id]
            );
            session()->flash('error', 'Une erreur est survenue lors de la validation de la feuille de temps.');
        }
    }

    /**
     * Rejeter la feuille de temps
     */
    public function rejeter()
    {
        $this->validate(['motifRejet' => 'required|string|min:5']);
        $user_connect = Auth::user();

        try {
            $comment = 'Feuille de temps rejetée par ' . $user_connect->name . '. Motif: ' . $this->motifRejet;

            //--- Workflow ---
            $workflow = WorkflowStub::make(FeuilleTempsWorkflow::class);
            $workflow->start($this->operation, 'rejeter', [
                'comment' => $comment,
                'motif_rejet' => $this->motifRejet,
                'user' => $user_connect->name
            ]);

            while ($workflow->running());

            if ($workflow->failed()) {
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow de rejet de la feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                    ['operation' => $this->operation->id]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow de rejet.');
            } else {
                Log::channel('daily')->info("La feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " vient d'être rejetée par l'utilisateur " . $user_connect->name, ['operation' => $this->operation->id]);
                session()->flash('success', 'Feuille de temps rejetée avec succès.');
                $this->showRejectModal = false;
                $this->motifRejet = '';
                $this->mount();
            }
        } catch (\Throwable $th) {
            Log::channel('daily')->error(
                "Erreur lors du rejet de la feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'operation' => $this->operation->id]
            );
            session()->flash('error', 'Une erreur est survenue lors du rejet de la feuille de temps.');
        }
    }

    /**
     * Retourner la feuille de temps (admin)
     */
    public function retourner()
    {
        $this->validate(['motifRejet' => 'required|string|min:5']);
        $user_connect = Auth::user();

        try {
            $comment = 'Feuille de temps retournée par ' . $user_connect->name . '. Motif: ' . $this->motifRejet;

            //--- Workflow ---
            $workflow = WorkflowStub::make(FeuilleTempsWorkflow::class);
            $workflow->start($this->operation, 'retourner', [
                'comment' => $comment,
                'motif_rejet' => $this->motifRejet,
                'user' => $user_connect->name
            ]);

            while ($workflow->running());

            if ($workflow->failed()) {
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow de retour de la feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                    ['operation' => $this->operation->id]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow de retour.');
            } else {
                Log::channel('daily')->info("La feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " vient d'être retournée par l'utilisateur " . $user_connect->name, ['operation' => $this->operation->id]);
                session()->flash('success', 'Feuille de temps retournée avec succès.');
                $this->showReturnModal = false;
                $this->motifRejet = '';
                $this->mount();
            }
        } catch (\Throwable $th) {
            Log::channel('daily')->error(
                "Erreur lors du retour de la feuille de temps de l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'operation' => $this->operation->id]
            );
            session()->flash('error', 'Une erreur est survenue lors du retour de la feuille de temps.');
        }
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatutFormate()
    {
        return match ($this->operation->workflow_state) {
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

    // Garder toutes les autres méthodes existantes sans modification...

    /**
     * Calculer la banque de temps dynamique (version complète avec collectif)
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

        // 1. RECHERCHE INDIVIDUELLE : Configurations spécifiques à cet employé
        $configurationsIndividuelles = Configuration::with('codeTravail')
            ->where('employe_id', $this->employe->id)
            ->where('annee_budgetaire_id', $anneeFinanciere->id)
            ->whereHas('codeTravail', function ($query) {
                $query->where('est_banque', true);
            })
            ->get();

        // Créer un tableau des codes déjà trouvés en individuel
        $codesIndividuels = $configurationsIndividuelles->pluck('code_travail_id')->toArray();

        // 2. RECHERCHE COLLECTIVE : Configurations collectives (employe_id = NULL)
        $configurationsCollectives = Configuration::with(['codeTravail', 'employes'])
            ->whereNull('employe_id')
            ->whereNull('date')
            ->where('annee_budgetaire_id', $anneeFinanciere->id)
            ->whereHas('codeTravail', function ($query) {
                $query->where('est_banque', true);
            })
            ->whereHas('employes', function ($query) {
                $query->where('employe_id', $this->employe->id);
            })
            ->whereNotIn('code_travail_id', $codesIndividuels) // Exclure ceux déjà trouvés en individuel
            ->get();

        // 3. TRAITEMENT DES CONFIGURATIONS INDIVIDUELLES
        foreach ($configurationsIndividuelles as $config) {
            $codeTravail = $config->codeTravail;

            if ($codeTravail->cumule_banque) {
                $valeur = $config->quota ?? 0;
            } else {
                $valeur = $config->reste ?? 0;
            }

            $banqueTemps[] = [
                'type' => $codeTravail->code,
                'libelle' => $codeTravail->libelle,
                'valeur' => $valeur,
                'code_travail' => $codeTravail,
                'configuration' => $config,
                'utilise_quota' => $codeTravail->cumule_banque,
                'est_collectif' => false // Pour debug
            ];
        }

        // 4. TRAITEMENT DES CONFIGURATIONS COLLECTIVES
        foreach ($configurationsCollectives as $config) {
            $codeTravail = $config->codeTravail;

            if ($codeTravail->cumule_banque) {
                $valeur = $config->quota ?? 0;
            } else {
                $valeur = $config->reste ?? 0;
            }

            $banqueTemps[] = [
                'type' => $codeTravail->code,
                'libelle' => $codeTravail->libelle,
                'valeur' => $valeur,
                'code_travail' => $codeTravail,
                'configuration' => $config,
                'utilise_quota' => $codeTravail->cumule_banque,
                'est_collectif' => true // Pour debug
            ];
        }

        // Trier par libellé pour un affichage cohérent
        usort($banqueTemps, function ($a, $b) {
            return strcmp($a['libelle'], $b['libelle']);
        });

        $this->banqueDeTemps = $banqueTemps;
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
        usort($recapitulatif, function ($a, $b) {
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
        // Vider le tableau avant de le remplir pour éviter les doublons
        $this->datesSemaine = [];

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

    /**
     * Charger les heures définies pour l'employé
     */
    private function chargerHeuresDefiniesEmploye()
    {
        $historiqueHeure = DB::table('historique_heures_semaines')
            ->where('employe_id', $this->employe->id)
            ->where('date_debut', '<=', now())
            ->orderBy('date_debut', 'desc')
            ->first();

        $this->heuresDefiniesEmploye = $historiqueHeure ? $historiqueHeure->nombre_d_heure_semaine : 35;
    }

    /**
     * Calculer les détails des heures supplémentaires à partir de l'opération
     */
    private function calculerDetailsHeuresSupplementaires()
    {
        // Récupérer les données depuis l'opération
        $this->heuresTravaillees = $this->operation->total_heure ?? 0;
        $this->totalHeuresSupAjustees = $this->operation->total_heure_supp_ajuster ?? 0;
        $heuresSupAPayer = $this->operation->total_heure_sup_a_payer ?? 0;

        $heuresDefinies = $this->heuresDefiniesEmploye;

        // Réinitialiser les valeurs
        $this->heuresSupNormales = 0;
        $this->heuresSupMajorees = 0;
        $heuresManquantes = 0;

        // Calculer les détails selon la logique canadienne
        if ($this->heuresTravaillees < $heuresDefinies) {
            // CAS 1: Heures manquantes
            $heuresManquantes = $heuresDefinies - $this->heuresTravaillees;
            $message = "Heures manquantes: {$heuresDefinies}h - {$this->heuresTravaillees}h = {$heuresManquantes}h (employé doit à l'entreprise)";
        } else if ($this->heuresTravaillees == $heuresDefinies) {
            // CAS 2: Heures exactes
            $message = "Heures exactes (= heures définies)";
        } else if ($this->heuresTravaillees <= 40) {
            // CAS 3: Heures sup. normales seulement
            $this->heuresSupNormales = $this->heuresTravaillees - $heuresDefinies;
            $message = "Heures sup. normales: {$this->heuresTravaillees}h - {$heuresDefinies}h = {$this->heuresSupNormales}h";
        } else {
            // CAS 4: Heures sup. normales + majorées
            $this->heuresSupNormales = 40 - $heuresDefinies;
            $this->heuresSupMajorees = ($this->heuresTravaillees - 40) * 1.5;
            $message = "Heures sup. normales: 40h - {$heuresDefinies}h = {$this->heuresSupNormales}h | Heures sup. majorées: ({$this->heuresTravaillees}h - 40h) × 1.5 = {$this->heuresSupMajorees}h";
        }

        // Calculer l'ajustement de la banque de temps 
        if ($heuresManquantes > 0) {
            // CAS 1: Heures manquantes - soustraction de la banque
            $this->versBanqueTemps = $this->totalHeuresSupAjustees - $heuresSupAPayer - $heuresManquantes;
        } else {
            // CAS 2: Heures supplémentaires - calcul normal
            $this->versBanqueTemps = $this->totalHeuresSupAjustees - $heuresSupAPayer;
        }

        $differenceHebdomadaire = $this->heuresTravaillees - $heuresDefinies;
        $this->ajustementBanque = $differenceHebdomadaire - $heuresSupAPayer;

        // Debug data
        $this->debugCalculs = [
            'heures_travaillees' => $this->heuresTravaillees,
            'heures_definies' => $heuresDefinies,
            'heures_sup_normales' => $this->heuresSupNormales,
            'heures_sup_majorees' => $this->heuresSupMajorees,
            'total_heures_sup_ajustees' => $this->totalHeuresSupAjustees,
            'heures_sup_a_payer' => $heuresSupAPayer,
            'vers_banque_temps' => $this->versBanqueTemps,
            'difference_hebdomadaire' => $differenceHebdomadaire,
            'ajustement_banque' => $this->ajustementBanque,
            'heures_manquantes' => $heuresManquantes,
            'message' => $message
        ];
    }

    /**
     * Convertir une valeur décimale en format d'affichage (00.00)
     */
    private function formatDecimalToDisplay($value)
    {
        if (empty($value) || $value == 0) {
            return '00.00';
        }

        $floatValue = floatval($value);
        return sprintf('%05.2f', $floatValue);
    }

    /**
     * Calculer la nouvelle valeur de la banque de temps après ajustement (pour affichage)
     */
    public function getNouveauSoldeBanqueTempsProperty()
    {
        // Récupérer la banque de temps actuelle pour l'affichage
        $banqueActuelle = collect($this->banqueDeTemps)->firstWhere('type', 'banque_temps');
        $soldeActuel = $banqueActuelle ? $banqueActuelle['valeur'] : 0;

        return $soldeActuel + $this->ajustementBanque;
    }

    /**
     * Mettre à jour la banque de temps lors de la validation
     */
    private function mettreAJourBanqueTemps()
    {
        // Utiliser la valeur versBanqueTemps déjà calculée dans calculerDetailsHeuresSupplementaires()
        $versBanqueTemps = $this->versBanqueTemps;

        if ($versBanqueTemps == 0) {
            return; // Pas d'ajustement, rien à faire
        }

        $config = $this->getConfigurationBanqueTemps();

        if (!$config) {
            // Créer une nouvelle configuration si elle n'existe pas
            $codeCaiss = CodeTravail::where('est_banque', true)
                ->where('cumule_banque', true)
                ->first();

            if ($codeCaiss) {
                $anneeFinanciere = AnneeFinanciere::where('actif', true)->first();

                if ($anneeFinanciere) {
                    Configuration::create([
                        'libelle' => $codeCaiss->libelle,
                        'quota' => $versBanqueTemps, // Utiliser la valeur déjà calculée
                        'consomme' => 0,
                        'reste' => $versBanqueTemps,
                        'employe_id' => $this->employe->id,
                        'annee_budgetaire_id' => $anneeFinanciere->id,
                        'code_travail_id' => $codeCaiss->id,
                        'commentaire' => "Ajustement semaine {$this->semaine->numero_semaine} - Vers banque: " .
                            ($versBanqueTemps > 0 ? '+' : '') . $versBanqueTemps . 'h - ' . now()->format('d/m/Y')
                    ]);
                }
            } else {
                // Log d'erreur si aucun code de banque cumulative n'est trouvé
                \Log::warning("Aucun code de travail avec cumule_banque=true trouvé pour la banque de temps");
            }
        } else {
            // Mettre à jour SEULEMENT la colonne quota
            $nouveauQuota = $config->quota + $versBanqueTemps;

            $config->update([
                'quota' => $nouveauQuota, // Mise à jour seulement du quota
                'commentaire' => ($config->commentaire ?? '') . "\nAjustement semaine {$this->semaine->numero_semaine}: " .
                    "Vers banque " . ($versBanqueTemps > 0 ? '+' : '') . $versBanqueTemps . 'h - ' . now()->format('d/m/Y')
            ]);
        }
    }

    /**
     * Obtenir la configuration de la banque de temps (code avec cumule_banque = true)
     */
    private function getConfigurationBanqueTemps()
    {
        $anneeFinanciere = AnneeFinanciere::where('actif', true)->first();

        if (!$anneeFinanciere) {
            return null;
        }

        // Rechercher la configuration avec cumule_banque = true (généralement CAISS)
        $config = Configuration::with('codeTravail')
            ->where('employe_id', $this->employe->id)
            ->where('annee_budgetaire_id', $anneeFinanciere->id)
            ->whereHas('codeTravail', function ($query) {
                $query->where('est_banque', true)
                    ->where('cumule_banque', true); // Code qui cumule les heures
            })
            ->first();

        return $config;
    }

    /**
     * Calculer les jours fériés pour la semaine
     */
    private function calculerJoursFeries()
    {
        // Récupérer l'année financière active
        $anneeFinanciere = AnneeFinanciere::where('actif', true)->first();

        if (!$anneeFinanciere) {
            $this->joursFeries = [];
            return;
        }

        // Récupérer les dates de jours fériés depuis les configurations
        $datesFeries = Configuration::where('annee_budgetaire_id', $anneeFinanciere->id)
            ->whereNotNull('date')
            ->pluck('date')
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // Vérifier quelles dates de la semaine sont des jours fériés
        $this->joursFeries = [];
        foreach ($this->datesSemaine as $index => $dateInfo) {
            $dateFormatee = $dateInfo['date']->format('Y-m-d');
            if (in_array($dateFormatee, $datesFeries)) {
                $this->joursFeries[] = $index; // Stocker l'index du jour férié
            }
        }
    }

    /**
     * Vérifier si un jour est férié
     */
    public function estJourFerie($jourIndex)
    {
        return in_array($jourIndex, $this->joursFeries);
    }

    private function traiterCodesDeductiblesValidation()
    {
        try {
            Log::channel('daily')->info("Début traitement codes déductibles pour validation", [
                'operation_id' => $this->operation->id,
                'employe_id' => $this->employe->id
            ]);

            // Récupérer l'année financière active
            $anneeFinanciere = AnneeFinanciere::where('actif', true)->first();
            if (!$anneeFinanciere) {
                Log::channel('daily')->error("Année financière active non trouvée");
                return false;
            }

            $totalTraite = 0;
            $erreursValidation = [];

            // Parcourir toutes les lignes de travail de cette opération
            foreach ($this->operation->lignesTravail as $ligneTravail) {
                $codeTravail = $ligneTravail->codeTravail;

                // Vérifier si le code de travail est déductible
                if ($codeTravail && $codeTravail->est_deductible) {
                    $totalHeuresLigne = $ligneTravail->getTotalHeures();

                    if ($totalHeuresLigne > 0) {
                        Log::channel('daily')->info("Traitement code déductible", [
                            'code_travail_id' => $codeTravail->id,
                            'code' => $codeTravail->code,
                            'total_heures' => $totalHeuresLigne
                        ]);

                        // 1. RECHERCHE INDIVIDUELLE : Configuration spécifique à cet employé
                        $configuration = Configuration::where('employe_id', $this->employe->id)
                            ->where('annee_budgetaire_id', $anneeFinanciere->id)
                            ->where('code_travail_id', $codeTravail->id)
                            ->first();

                        if ($configuration) {
                            // CAS INDIVIDUEL
                            $this->traiterConfigurationIndividuelle($configuration, $totalHeuresLigne, $codeTravail, $erreursValidation);
                        } else {
                            // 2. RECHERCHE COLLECTIVE : Configuration collective (employe_id = NULL)
                            $configurationCollective = Configuration::whereNull('employe_id')
                                ->where('annee_budgetaire_id', $anneeFinanciere->id)
                                ->where('code_travail_id', $codeTravail->id)
                                ->whereHas('employes', function ($query) {
                                    $query->where('employe_id', $this->employe->id);
                                })
                                ->first();

                            if ($configurationCollective) {
                                // CAS COLLECTIF
                                $this->traiterConfigurationCollective($configurationCollective, $totalHeuresLigne, $codeTravail, $erreursValidation);
                            } else {
                                Log::channel('daily')->warning("Configuration non trouvée pour code déductible", [
                                    'code_travail_id' => $codeTravail->id,
                                    'employe_id' => $this->employe->id
                                ]);
                            }
                        }

                        $totalTraite += $totalHeuresLigne;
                    }
                }
            }

            // Vérifier s'il y a des erreurs de validation
            if (!empty($erreursValidation)) {
                $messageErreur = "Solde insuffisant pour :\n";
                foreach ($erreursValidation as $erreur) {
                    $messageErreur .= "- {$erreur['libelle']} : {$erreur['demande']}h demandées, {$erreur['disponible']}h disponibles\n";
                }

                Log::channel('daily')->error("Erreurs de validation codes déductibles", [
                    'erreurs' => $erreursValidation
                ]);

                throw new \Exception($messageErreur);
            }

            Log::channel('daily')->info("Traitement codes déductibles terminé", [
                'total_heures_traitees' => $totalTraite
            ]);

            return true;
        } catch (\Throwable $th) {
            Log::channel('daily')->error("Erreur lors du traitement des codes déductibles", [
                'operation_id' => $this->operation->id,
                'message' => $th->getMessage()
            ]);
            throw $th;
        }
    }

    /**
     * Traiter une configuration individuelle
     */
    private function traiterConfigurationIndividuelle($configuration, $totalHeuresLigne, $codeTravail, &$erreursValidation)
    {
        $resteActuel = $configuration->reste ?? 0;

        if ($totalHeuresLigne > $resteActuel) {
            $erreursValidation[] = [
                'code' => $codeTravail->code,
                'libelle' => $codeTravail->libelle,
                'demande' => $totalHeuresLigne,
                'disponible' => $resteActuel
            ];
            return;
        }

        // Mettre à jour consommé et reste
        $nouveauConsomme = ($configuration->consomme ?? 0) + $totalHeuresLigne;
        $nouveauReste = $resteActuel - $totalHeuresLigne;

        $configuration->update([
            'consomme' => $nouveauConsomme,
            'reste' => $nouveauReste,
            'commentaire' => ($configuration->commentaire ?? '') .
                "\nValidation semaine {$this->semaine->numero_semaine}: -{$totalHeuresLigne}h - " .
                now()->format('d/m/Y H:i')
        ]);

        Log::channel('daily')->info("Configuration individuelle mise à jour", [
            'configuration_id' => $configuration->id,
            'code_travail' => $codeTravail->code,
            'heures_consommees' => $totalHeuresLigne,
            'nouveau_consomme' => $nouveauConsomme,
            'nouveau_reste' => $nouveauReste
        ]);
    }

    /**
     * Traiter une configuration collective
     */
    private function traiterConfigurationCollective($configurationCollective, $totalHeuresLigne, $codeTravail, &$erreursValidation)
    {
        // Pour le collectif, calculer le reste disponible pour cet employé
        $consommeIndividuel = DB::table('configuration_employe')
            ->where('configuration_id', $configurationCollective->id)
            ->where('employe_id', $this->employe->id)
            ->value('consomme_individuel') ?? 0;

        $resteDisponible = ($configurationCollective->quota ?? 0) - $consommeIndividuel;

        if ($totalHeuresLigne > $resteDisponible) {
            $erreursValidation[] = [
                'code' => $codeTravail->code,
                'libelle' => $codeTravail->libelle . ' (collectif)',
                'demande' => $totalHeuresLigne,
                'disponible' => $resteDisponible
            ];
            return;
        }

        // Mettre à jour le consommé individuel dans la table configuration_employe
        DB::table('configuration_employe')
            ->where('configuration_id', $configurationCollective->id)
            ->where('employe_id', $this->employe->id)
            ->update([
                'consomme_individuel' => $consommeIndividuel + $totalHeuresLigne,
                'updated_at' => now()
            ]);

        // Mettre à jour le total consommé dans la configuration collective
        $totalConsommeCollectif = DB::table('configuration_employe')
            ->where('configuration_id', $configurationCollective->id)
            ->sum('consomme_individuel');

        $configurationCollective->update([
            'consomme' => $totalConsommeCollectif,
            'reste' => ($configurationCollective->quota ?? 0) - $totalConsommeCollectif,
            'commentaire' => ($configurationCollective->commentaire ?? '') .
                "\nValidation semaine {$this->semaine->numero_semaine}: -{$totalHeuresLigne}h employé {$this->employe->nom} - " .
                now()->format('d/m/Y H:i')
        ]);

        Log::channel('daily')->info("Configuration collective mise à jour", [
            'configuration_id' => $configurationCollective->id,
            'code_travail' => $codeTravail->code,
            'heures_consommees' => $totalHeuresLigne,
            'nouveau_consomme_individuel' => $consommeIndividuel + $totalHeuresLigne,
            'total_consomme_collectif' => $totalConsommeCollectif,
            'reste_disponible_apres' => $resteDisponible - $totalHeuresLigne
        ]);
    }

    public function render()
    {
        return view('rhfeuilledetempsreguliere::livewire.rh-feuille-de-temps-reguliere-show');
    }
}
