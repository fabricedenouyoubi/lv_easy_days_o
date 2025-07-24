<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsAbsence\Traits\AbsenceResource;
use Modules\RhFeuilleDeTempsAbsence\Workflows\DemandeAbsenceWorkflow;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;
use Modules\RhFeuilleDeTempsReguliere\Models\LigneTravail;
use Workflow\WorkflowStub;

class RhFeuilleDeTempsAbsenceDetails extends Component
{
    use WithPagination, AbsenceResource;
    public $demandeAbsenceId;
    public $demandeAbsence;
    public $nombreJourAbsence;
    public $workflow_log;
    public $motif;

    public $showEditAbsenceModal = false;
    public $showSoumissionModal = false;
    public $showRappelerModal = false;
    public $showApprouverModal = false;
    public $showRetournerModal = false;
    public $showRejeterModal = false;

    public $jours_non_ouvrable;

    public $banqueTemps = [];
    public $employe;
    // Banque de temps
    public $banqueDeTemps = [];


    public function messages()
    {
        return [
            'motif.required' => 'Le motif est obligatoire.',
            'motif.string' => 'Le motif doit être une chaîne de caractères.',
            'motif.min' => 'Le motif doit contenir au moins :min caractères.',
        ];
    }

    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'demandeAbsenceModifie' => 'demandeAbsenceModifie',
        'nombreDeJoursEntre' => 'handleNombreDeJoursEntre'
    ];

    public function mount()
    {
        $this->demandeAbsence = DemandeAbsence::with('employe', 'codeTravail', 'operations', 'operations.anneeSemaine', 'anneeFinanciere')->findOrFail($this->demandeAbsenceId);
        $this->workflow_log = $this->demandeAbsence->workflow_log;
        $this->nombreJourAbsence = $this->nombreDeJoursEntre($this->demandeAbsence->date_debut, $this->demandeAbsence->date_fin, $this->demandeAbsence->annee_financiere_id);

        // Récupérer l'employé connecté
        $this->employe = Auth::user()->employe;

        // Calculer la banque de temps
        $this->calculerBanqueDeTemps();
    }

    //--- afficher et caher le formulaire d'ajout d'une absence
    public function toogle_edit_absence_modal()
    {
        $this->showEditAbsenceModal = !$this->showEditAbsenceModal;
    }

    //--- afficher et caher le formulaire de soumission d'une demande absence
    public function toogle_soumission_modal()
    {
        $this->showSoumissionModal = !$this->showSoumissionModal;
    }

    //--- afficher et caher le formulaire de rappele d'une demande absence
    public function toogle_rappeler_modal()
    {
        $this->showRappelerModal = !$this->showRappelerModal;
    }

    //--- afficher et caher le formulaire d'approbation d'une demande absence
    public function toogle_approve_modal()
    {
        $this->showApprouverModal = !$this->showApprouverModal;
    }

    //--- afficher et caher le formulaire d'approbation d'une demande absence
    public function toogle_retrouner_modal()
    {
        $this->showRetournerModal = !$this->showRetournerModal;
    }

    //--- afficher et caher le formulaire de rejet d'une demande absence
    public function toogle_rejeter_modal()
    {
        $this->showRejeterModal = !$this->showRejeterModal;
    }

    //--- mise ajout du nombre de jour d'absence après la modificaion
    public function handleNombreDeJoursEntre($val = null)
    {
        if ($val) {
            $this->nombreJourAbsence = $val;
        }
    }

    //--- afficher le message de modification d'une absence
    public function demandeAbsenceModifie()
    {
        $this->showEditAbsenceModal = false;
        session()->flash('success', 'Demande d\'absence modifié avec succès.');
    }

    //--- Soumission de la demande d'absence
    public function soumettreDemandeAbsence()
    {
        $user_connect = Auth::user();
        try {

            $comment = 'La demande a été soumise par';

            //--- Workflow ---
            $workflow = WorkflowStub::make(DemandeAbsenceWorkflow::class);
            $workflow->start($this->demandeAbsence, 'soumettre', ['comment' => $comment, 'user' => Auth::user()->name]);

            while ($workflow->running());

            if ($workflow->failed()) {
                //--- Journalisation
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow de la soumission de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                    ['demande' => $this->demandeAbsence->id]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow de la soumission de la demande d\'absence.');
            } else {
                //--- Journalisation
                Log::channel('daily')->info("La demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " vient d'être soumise par l' utilisateur " . $user_connect->name, ['demande' => $this->demandeAbsence->id]);
                session()->flash('success', 'Demande d\'absence  soumise avec succès.');
                $this->showSoumissionModal = false;
                $this->mount();
            }
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors de la soumission de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'demande' => $this->demandeAbsence->id]
            );

            session()->flash('error', 'Une erreur est survenue lors de la soumission de la demande d\'absence.', $th->getMessage());
        }
    }

    //--- Rappelle de la demande d'absence
    public function rapelleDemandeAbsence()
    {
        $user_connect = Auth::user();
        try {
            $comment = 'La demande a été rappelée';

            //--- Workflow ---
            $workflow = WorkflowStub::make(DemandeAbsenceWorkflow::class);
            $workflow->start($this->demandeAbsence, 'rappeler', ['comment' => $comment, 'motif' => $this->motif, 'user' => Auth::user()->name]);

            while ($workflow->running());

            if ($workflow->failed()) {
                //--- Journalisation
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow du rappel de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                    ['demande' => $this->demandeAbsence->id]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow du rappel de la demande d\'absence.');
            } else {
                //--- Journalisation
                Log::channel('daily')->info("La demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " vient d'être rappelée par l' utilisateur " . $user_connect->name, ['demande' => $this->demandeAbsence->id]);
                session()->flash('success', 'Demande d\'absence rappelée avec succès.');
                $this->showRappelerModal = false;
                $this->reset('motif');
                $this->mount();
            }
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors du rappel de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'demande' => $this->demandeAbsence->id]
            );

            session()->flush('error', 'Une erreur est survenue lors du rappel de la demande d\'absence.', $th->getMessage());
        }
    }

    //--- Approuver la demande d'absence
    public function approuverDemandeAbsence()
    {
        $user_connect = Auth::user();
        try {

            /* //--- Selection des semaines de l'année ---
            $semaines = SemaineAnnee::where('annee_financiere_id', $this->demandeAbsence->annee_financiere_id)
                ->where('fin', '>=', $this->demandeAbsence->date_debut)
                ->where('debut', '<=', $this->demandeAbsence->date_fin)
                ->get();

            //--- Récupération des jours non ouvrables pour l'année financière concernée ---
            $jours_non_ouvrable = collect($this->recupererDateNonOuvrable($this->demandeAbsence->annee_financiere_id))
                ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'));

            //--- Enregistrement des opérations
            foreach ($semaines as $semaine) {

                // On travaille uniquement avec les dates au format 'YYYY-MM-DD' sans l'heure
                $dateDebut = Carbon::parse($this->demandeAbsence->date_debut)->toDateString();
                $semaineDebut = Carbon::parse($semaine->debut)->toDateString();

                // On prend la date de début la plus tardive entre la demande d'absence et la semaine courante
                //Cela permet de ne pas dépasser les limites de la semaine lors du calcul
                $dateDebut = max($dateDebut, $semaineDebut);

                $dateFin = Carbon::parse($this->demandeAbsence->date_fin)->toDateString();
                $semaineFin = Carbon::parse($semaine->fin)->toDateString();

                // On prend la date de fin la plus tôt entre la demande d'absence et la semaine courante
                $dateFin = min($dateFin, $semaineFin);

                // Création d'une période
                $periode = CarbonPeriod::create($dateDebut, $dateFin);

                // Filtre la période pour compter uniquement les jours qui ne sont pas non ouvrables
                $jours_absence = collect($periode)->filter(function ($date) use ($jours_non_ouvrable) {
                    return !$jours_non_ouvrable->contains($date->format('Y-m-d'));
                })->count();

                // Création d'une opération (enregistrement dans la base) représentant l'absence sur cette semaine
                $operation = Operation::create([
                    'demande_absence_id' => $this->demandeAbsence->id,
                    //'annee_semaine_id' => $semaine->id,
                    'employe_id' => $this->demandeAbsence->employe_id,
                    'total_heure' => $jours_absence * $this->demandeAbsence->heure_par_jour,
                    'workflow_state' => 'valide',
                    'statut' => 'valide'
                ]);
            } */

            $comment = 'La demande est approuvée';
            //--- Workflow ---
            $workflow = WorkflowStub::make(DemandeAbsenceWorkflow::class);
            $workflow->start($this->demandeAbsence, 'valider', ['comment' => $comment, 'user' => Auth::user()->name]);

            while ($workflow->running());

            if ($workflow->failed()) {
                //--- Journalisation
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow de la validation de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                    ['demande' => $this->demandeAbsence->id]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow de la validation de la demande d\'absence.');
            } else {


                try {
                    $this->remplirFeuillesDeTempsAutomatiquement();
                    Log::channel('daily')->info("Feuilles de temps remplies automatiquement après validation de l'absence");
                } catch (\Throwable $fillError) {
                    Log::channel('daily')->error("Erreur lors du remplissage automatique des feuilles de temps", [
                        'demande_absence_id' => $this->demandeAbsence->id,
                        'message' => $fillError->getMessage()
                    ]);
                    // Ne pas faire échouer la validation, juste logger l'erreur
                    session()->flash('warning', 'Demande validée, mais erreur lors du remplissage automatique des feuilles de temps.');
                }

                //--- Journalisation
                Log::channel('daily')->info("La demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " vient d'être validée par l' utilisateur " . $user_connect->name, ['demande' => $this->demandeAbsence->id]);
                session()->flash('success', 'Demande d\'absence validée avec succès.');
                $this->showApprouverModal = false;
                $this->mount();
            }
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors de la validation de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'demande' => $this->demandeAbsence->id]
            );

            session()->flash('error', 'Une erreur est survenue lors de la validation de la demande d\'absence.' . $th->getMessage());
        }
    }

    //--- Retourner la demande d'absence
    public function retournerDemandeAbsence()
    {
        $this->validate(['motif' => ['required', 'string', 'min:5']]);
        $user_connect = Auth::user();

        try {

            if ($this->demandeAbsence->operations()->count() > 0) {
                $this->demandeAbsence->operations()->delete();
            }

            // NOUVEAU : Supprimer les lignes auto-remplies
            try {
                $this->supprimerLignesAutoRempliesAbsence();
                Log::channel('daily')->info("Lignes auto-remplies supprimées lors du retour de l'absence");
            } catch (\Throwable $deleteError) {
                Log::channel('daily')->error("Erreur lors de la suppression des lignes auto-remplies", [
                    'demande_absence_id' => $this->demandeAbsence->id,
                    'message' => $deleteError->getMessage()
                ]);
            }

            $comment = 'La demande a été retournée';

            //--- Workflow ---
            $workflow = WorkflowStub::make(DemandeAbsenceWorkflow::class);
            $workflow->start($this->demandeAbsence, 'retourner', ['comment' => $comment, 'motif' => $this->motif, 'user' => Auth::user()->name]);

            while ($workflow->running());

            if ($workflow->failed()) {
                //--- Journalisation
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow du retour de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                    ['demande' => $this->demandeAbsence->id]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow du retour de la demande d\'absence.');
            } else {

                //--- Journalisation
                Log::channel('daily')->info("La demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " vient d'être retournée par l' utilisateur " . $user_connect->name, ['demande' => $this->demandeAbsence->id]);
                session()->flash('success', 'Demande d\'absence retournée avec succès.');
                $this->showRetournerModal = false;
                $this->reset('motif');
                $this->mount();
            }
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors du retour de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'demande' => $this->demandeAbsence->id]
            );

            session()->flash('error', 'Une erreur est survenue lors du retour de la demande d\'absence.', $th->getMessage());
        }
    }

    //--- Rejeter la demande d'absence
    public function rejeterDemandeAbsence()
    {
        $this->validate(['motif' => ['required', 'string', 'min:5']]);
        $user_connect = Auth::user();

        try {
            //--- Suppresion des opérations et liaison avec la feuille de temps
            $this->demandeAbsence->operations()->delete();

            $comment = 'La demande a été rejetée';

            //--- Workflow ---
            $workflow = WorkflowStub::make(DemandeAbsenceWorkflow::class);
            $workflow->start($this->demandeAbsence, 'rejeter', ['comment' => $comment, 'motif' => $this->motif, 'user' => Auth::user()->name]);

            while ($workflow->running());

            if ($workflow->failed()) {
                //--- Journalisation
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow du retour de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                    ['demande' => $this->demandeAbsence->id]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow du rejet de la demande d\'absence.');
            } else {

                //--- Journalisation
                Log::channel('daily')->info("La demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " vient d'être rejetée par l' utilisateur " . $user_connect->name, ['demande' => $this->demandeAbsence->id]);
                session()->flash('success', 'Demande d\'absence rejetée avec succès.');
                $this->showRejeterModal = false;
                $this->reset('motif');
                $this->mount();
            }
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors du rejet de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'demande' => $this->demandeAbsence->id]
            );

            session()->flash('error', 'Une erreur est survenue lors du rejet de la demande d\'absence.', $th->getMessage());
        }
    }


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
     * Remplir automatiquement les feuilles de temps lors de la validation d'une absence
     */
    private function remplirFeuillesDeTempsAutomatiquement()
    {
        try {
            Log::channel('daily')->info("Début du remplissage automatique des feuilles de temps", [
                'demande_absence_id' => $this->demandeAbsence->id,
                'employe_id' => $this->demandeAbsence->employe_id,
                'date_debut' => $this->demandeAbsence->date_debut,
                'date_fin' => $this->demandeAbsence->date_fin
            ]);

            // 1. Récupérer les semaines concernées par l'absence
            $semainesConcernees = $this->getSemainesConcerneesPourAbsence();

            if ($semainesConcernees->isEmpty()) {
                Log::channel('daily')->warning("Aucune semaine trouvée pour la période d'absence");
                return;
            }

            // 2. Récupérer les jours non ouvrables (fériés)
            $joursNonOuvrables = $this->recupererJoursNonOuvrables();

            // 3. Pour chaque semaine, créer ou mettre à jour l'opération et les lignes
            foreach ($semainesConcernees as $semaine) {
                $this->traiterSemainePourAbsence($semaine, $joursNonOuvrables);
            }

            Log::channel('daily')->info("Remplissage automatique terminé avec succès", [
                'nombre_semaines_traitees' => $semainesConcernees->count()
            ]);
        } catch (\Throwable $th) {
            Log::channel('daily')->error("Erreur lors du remplissage automatique des feuilles de temps", [
                'demande_absence_id' => $this->demandeAbsence->id,
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            throw $th;
        }
    }

    /**
     * Récupérer les semaines concernées par la période d'absence
     */
    private function getSemainesConcerneesPourAbsence()
    {
        return SemaineAnnee::where('annee_financiere_id', $this->demandeAbsence->annee_financiere_id)
            ->where('fin', '>=', $this->demandeAbsence->date_debut->toDateString())
            ->where('debut', '<=', $this->demandeAbsence->date_fin->toDateString())
            ->orderBy('numero_semaine')
            ->get();
    }

    /**
     * Récupérer les jours non ouvrables (jours fériés) pour l'année financière
     */
    private function recupererJoursNonOuvrables()
    {
        return Configuration::where('annee_budgetaire_id', $this->demandeAbsence->annee_financiere_id)
            ->whereNotNull('date')
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * Traiter une semaine spécifique pour l'absence
     */
    private function traiterSemainePourAbsence(SemaineAnnee $semaine, array $joursNonOuvrables)
    {
        try {
            Log::channel('daily')->info("Traitement de la semaine", [
                'semaine_id' => $semaine->id,
                'numero_semaine' => $semaine->numero_semaine,
                'debut' => $semaine->debut,
                'fin' => $semaine->fin
            ]);

            // 1. Obtenir ou créer l'opération pour cette semaine
            $operation = $this->obtenirOuCreerOperation($semaine);

            // 2. Calculer les dates effectives d'absence pour cette semaine
            $datesEffectives = $this->calculerDatesEffectivesPourSemaine($semaine);

            // 3. Calculer les heures d'absence pour cette semaine
            $heuresAbsence = $this->calculerHeuresAbsencePourSemaine($datesEffectives, $joursNonOuvrables);

            if ($heuresAbsence > 0) {
                // 4. Créer ou mettre à jour la ligne de travail pour l'absence
                $this->creerOuMettreAJourLigneTravailAbsence($operation, $datesEffectives, $joursNonOuvrables);

                // 5. Mettre à jour les totaux de l'opération
                $this->mettreAJourTotauxOperation($operation);

                Log::channel('daily')->info("Semaine traitée avec succès", [
                    'semaine_id' => $semaine->id,
                    'heures_absence' => $heuresAbsence,
                    'operation_id' => $operation->id
                ]);
            } else {
                Log::channel('daily')->info("Aucune heure d'absence pour cette semaine", [
                    'semaine_id' => $semaine->id
                ]);
            }
        } catch (\Throwable $th) {
            Log::channel('daily')->error("Erreur lors du traitement de la semaine", [
                'semaine_id' => $semaine->id,
                'message' => $th->getMessage()
            ]);
            throw $th;
        }
    }

    /**
     * Obtenir ou créer une opération pour la semaine et l'employé - VERSION CORRIGÉE
     */
    private function obtenirOuCreerOperation(SemaineAnnee $semaine)
    {
        // D'abord, calculer les heures totales pour cette semaine
        $datesEffectives = $this->calculerDatesEffectivesPourSemaine($semaine);
        $joursNonOuvrables = $this->recupererJoursNonOuvrables();
        $heuresAbsence = $this->calculerHeuresAbsencePourSemaine($datesEffectives, $joursNonOuvrables);

        $operation = Operation::firstOrCreate(
            [
                'employe_id' => $this->demandeAbsence->employe_id,
                'annee_semaine_id' => $semaine->id,
            ],
            [
                'workflow_state' => 'valide',
                'statut' => 'valide',
                'total_heure' => $heuresAbsence,
                'total_heure_regulier' => 0,
                'total_heure_supp' => 0,
                'total_heure_deplacement' => 0,
                'total_heure_formation' => 0,
                'total_heure_csn' => 0,
                'total_heure_caisse' => 0,
                'total_heure_conge_mobile' => 0,
                'demande_absence_id' => $this->demandeAbsence->id,
            ]
        );

        // Si l'opération existait déjà, mettre à jour l'ID de l'absence
        if (!$operation->wasRecentlyCreated && !$operation->demande_absence_id) {
            $operation->update([
                'demande_absence_id' => $this->demandeAbsence->id,
                'total_heure' => $operation->total_heure + $heuresAbsence
            ]);
        }

        Log::channel('daily')->info("Opération créée ou récupérée", [
            'operation_id' => $operation->id,
            'semaine_id' => $semaine->id,
            'employe_id' => $this->demandeAbsence->employe_id,
            'heures_absence' => $heuresAbsence,
            'demande_absence_id' => $operation->demande_absence_id,
            'was_recently_created' => $operation->wasRecentlyCreated
        ]);

        return $operation;
    }

    /**
     * Calculer les dates effectives d'absence pour une semaine donnée
     */
    private function calculerDatesEffectivesPourSemaine(SemaineAnnee $semaine)
    {
        $debutAbsence = $this->demandeAbsence->date_debut->copy();
        $finAbsence = $this->demandeAbsence->date_fin->copy();
        $debutSemaine = Carbon::parse($semaine->debut);
        $finSemaine = Carbon::parse($semaine->fin);

        // Prendre les dates qui se chevauchent entre l'absence et la semaine
        $dateDebutEffective = $debutAbsence->gt($debutSemaine) ? $debutAbsence : $debutSemaine;
        $dateFinEffective = $finAbsence->lt($finSemaine) ? $finAbsence : $finSemaine;

        return [
            'debut' => $dateDebutEffective,
            'fin' => $dateFinEffective
        ];
    }

    /**
     * Calculer les heures d'absence pour une semaine en excluant les jours non ouvrables
     */
    private function calculerHeuresAbsencePourSemaine(array $datesEffectives, array $joursNonOuvrables)
    {
        $dateDebut = $datesEffectives['debut'];
        $dateFin = $datesEffectives['fin'];

        $periode = CarbonPeriod::create($dateDebut, $dateFin);
        $joursOuvrables = 0;

        foreach ($periode as $date) {
            $dateFormatee = $date->format('Y-m-d');

            // Exclure les dimanches et les jours fériés
            if (!$date->isSunday() && !in_array($dateFormatee, $joursNonOuvrables)) {
                $joursOuvrables++;
            }
        }

        return $joursOuvrables * $this->demandeAbsence->heure_par_jour;
    }

    /**
     * Créer ou mettre à jour la ligne de travail pour l'absence - VERSION SIMPLIFIÉE
     */
    private function creerOuMettreAJourLigneTravailAbsence(Operation $operation, array $datesEffectives, array $joursNonOuvrables)
    {
        try {
            Log::channel('daily')->info("Début création/mise à jour ligne de travail", [
                'operation_id' => $operation->id,
                'codes_travail_id' => $this->demandeAbsence->codes_travail_id,
                'demande_absence_id' => $this->demandeAbsence->id
            ]);

            // Vérifier si une ligne existe déjà pour ce code de travail dans cette opération
            $ligneExistante = LigneTravail::where('operation_id', $operation->id)
                ->where('codes_travail_id', $this->demandeAbsence->codes_travail_id)
                ->where('auto_rempli', true)
                ->where('type_auto_remplissage', 'absence')
                ->first();

            if ($ligneExistante) {
                Log::channel('daily')->info("Mise à jour de la ligne de travail existante", [
                    'ligne_id' => $ligneExistante->id
                ]);
                $ligne = $ligneExistante;
            } else {
                Log::channel('daily')->info("Création d'une nouvelle ligne de travail");
                $ligne = new LigneTravail();
            }

            // Définir tous les attributs 
            $ligne->operation_id = $operation->id;
            $ligne->codes_travail_id = $this->demandeAbsence->codes_travail_id;
            $ligne->auto_rempli = true;
            $ligne->type_auto_remplissage = 'absence';

            // Réinitialiser toutes les durées à 0
            for ($jour = 0; $jour <= 6; $jour++) {
                $ligne->{"duree_{$jour}"} = 0.00;
            }

            Log::channel('daily')->info("Données de base de la ligne définies", [
                'operation_id' => $ligne->operation_id,
                'codes_travail_id' => $ligne->codes_travail_id,
                'auto_rempli' => $ligne->auto_rempli
            ]);

            // Remplir les jours concernés par l'absence
            $this->remplirJoursAbsenceDansLigne($ligne, $datesEffectives, $joursNonOuvrables);

            // Sauvegarder la ligne
            $saved = $ligne->save();

            if ($saved) {
                Log::channel('daily')->info("Ligne de travail sauvegardée avec succès", [
                    'ligne_id' => $ligne->id,
                    'total_heures' => $ligne->getTotalHeures(),
                    'operation_id' => $ligne->operation_id
                ]);
            } else {
                Log::channel('daily')->error("Échec de la sauvegarde de la ligne de travail");
                throw new \Exception("Impossible de sauvegarder la ligne de travail");
            }
        } catch (\Throwable $th) {
            Log::channel('daily')->error("Erreur lors de la création/mise à jour de la ligne de travail", [
                'operation_id' => $operation->id,
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            throw $th;
        }
    }

    /**
     * Remplir les jours d'absence dans la ligne de travail
     */
    private function remplirJoursAbsenceDansLigne(LigneTravail $ligne, array $datesEffectives, array $joursNonOuvrables)
    {
        $dateDebut = $datesEffectives['debut'];
        $dateFin = $datesEffectives['fin'];

        $periode = CarbonPeriod::create($dateDebut, $dateFin);

        foreach ($periode as $date) {
            $dateFormatee = $date->format('Y-m-d');

            // Exclure les dimanches et les jours fériés
            if (!$date->isSunday() && !in_array($dateFormatee, $joursNonOuvrables)) {
                // Déterminer le jour de la semaine (0=Lundi, 6=Dimanche)
                $jourSemaine = $date->dayOfWeek === 0 ? 6 : $date->dayOfWeek - 1;

                // Seulement les jours ouvrables (Lundi à Samedi, sauf dimanche)
                if ($jourSemaine >= 0 && $jourSemaine <= 5) {
                    $heuresJour = min($this->demandeAbsence->heure_par_jour, 8); // Max 8h par jour
                    $ligne->{"duree_{$jourSemaine}"} = $heuresJour;

                    Log::channel('daily')->info("Jour rempli", [
                        'date' => $dateFormatee,
                        'jour_semaine' => $jourSemaine,
                        'heures' => $heuresJour
                    ]);
                }
            } else {
                Log::channel('daily')->info("Jour exclu (dimanche ou férié)", [
                    'date' => $dateFormatee,
                    'is_sunday' => $date->isSunday(),
                    'is_ferie' => in_array($dateFormatee, $joursNonOuvrables)
                ]);
            }
        }
    }

    /**
     * Mettre à jour les totaux de l'opération après ajout des lignes d'absence
     */
    private function mettreAJourTotauxOperation(Operation $operation)
    {
        // Recalculer le total des heures à partir de toutes les lignes de travail
        $operation->load('lignesTravail');

        $totalHeures = 0;
        $totalHeuresAbsence = 0;

        foreach ($operation->lignesTravail as $ligne) {
            $totalLigne = $ligne->getTotalHeures();
            $totalHeures += $totalLigne;

            // Si c'est une ligne d'absence auto-remplie
            if ($ligne->auto_rempli && $ligne->type_auto_remplissage === 'absence') {
                $totalHeuresAbsence += $totalLigne;
            }
        }

        $operation->update([
            'total_heure' => $totalHeures,
            // Vous pouvez ajouter d'autres totaux spécifiques selon vos besoins
        ]);

        Log::channel('daily')->info("Totaux de l'opération mis à jour", [
            'operation_id' => $operation->id,
            'total_heure' => $totalHeures,
            'total_heures_absence' => $totalHeuresAbsence
        ]);
    }

    /**
     * Supprimer les opérations et lignes auto-remplies en cas de rejet ou d'annulation - VERSION CORRIGÉE
     */
    private function supprimerLignesAutoRempliesAbsence()
    {
        try {
            Log::channel('daily')->info("Suppression des opérations et lignes auto-remplies pour l'absence", [
                'demande_absence_id' => $this->demandeAbsence->id
            ]);

            // 1. Récupérer toutes les opérations liées à cette absence
            $operationsASupprimer = Operation::where('demande_absence_id', $this->demandeAbsence->id)->get();

            $operationsIds = [];
            $lignesSupprimees = 0;

            foreach ($operationsASupprimer as $operation) {
                $operationsIds[] = $operation->id;

                // 2. Supprimer toutes les lignes auto-remplies de cette opération
                $lignesASupprimer = LigneTravail::where('operation_id', $operation->id)
                    ->where('auto_rempli', true)
                    ->where('type_auto_remplissage', 'absence')
                    ->get();

                foreach ($lignesASupprimer as $ligne) {
                    $ligne->delete();
                    $lignesSupprimees++;
                }

                // 3. Vérifier s'il reste d'autres lignes dans l'opération
                $autresLignes = LigneTravail::where('operation_id', $operation->id)->count();

                if ($autresLignes === 0) {
                    // Si plus de lignes, supprimer complètement l'opération
                    Log::channel('daily')->info("Suppression complète de l'opération", [
                        'operation_id' => $operation->id
                    ]);
                    $operation->delete();
                } else {
                    // Si il reste d'autres lignes, juste enlever la référence à l'absence et recalculer les totaux
                    Log::channel('daily')->info("Suppression de la référence à l'absence (autres lignes présentes)", [
                        'operation_id' => $operation->id,
                        'autres_lignes' => $autresLignes
                    ]);

                    $operation->update([
                        'demande_absence_id' => null
                    ]);

                    $this->mettreAJourTotauxOperation($operation);
                }
            }

            Log::channel('daily')->info("Suppression terminée", [
                'operations_traitees' => count($operationsIds),
                'lignes_supprimees' => $lignesSupprimees
            ]);
        } catch (\Throwable $th) {
            Log::channel('daily')->error("Erreur lors de la suppression des opérations et lignes auto-remplies", [
                'demande_absence_id' => $this->demandeAbsence->id,
                'message' => $th->getMessage()
            ]);
            throw $th;
        }
    }
    public function render()
    {
        //dd($this->demandeAbsence->getWorkflowHistory());
        return view(
            'rhfeuilledetempsabsence::livewire.rh-feuille-de-temps-absence-details',
            [
                'logs' => $this->demandeAbsence->getWorkflowHistory()
            ]
        );
    }
}
