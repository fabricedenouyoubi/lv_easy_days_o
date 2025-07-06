<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsAbsence\Traits\AbsenceResource;

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


    public function messages()
    {
        return [
            'motif.required' => 'Le motif est obligatoire.',
            'motif.string' => 'Le motif doit être une chaîne de caractères.',
            'motif.min' => 'Le motif doit contenir au moins :min caractères.',
        ];
    }

    public $statuts = [
        'Brouillon',
        'En cours',
        'Soumis',
        'Validé',
        'Rejeté'
    ];

    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'demandeAbsenceModifie' => 'demandeAbsenceModifie',
        'nombreDeJoursEntre' => 'handleNombreDeJoursEntre'
    ];

    public function mount()
    {
        $this->demandeAbsence = DemandeAbsence::with('employe', 'codeTravail', 'operations.anneeSemaine')->findOrFail($this->demandeAbsenceId);
        $this->workflow_log = $this->demandeAbsence->workflow_log;
        $this->nombreJourAbsence = $this->nombreDeJoursEntre($this->demandeAbsence->date_debut, $this->demandeAbsence->date_fin, $this->demandeAbsence->annee_financiere_id);
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

            $comment = 'La demande a été soumise par ' . Auth::user()->name;
            $this->demandeAbsence->applyTransition('soumettre', ['comment' => $comment]);
            $this->showSoumissionModal = false;

            //--- Journalisation
            Log::channel('daily')->info("La demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " vient d'être soumise par l' utilisateur " . $user_connect->name);

            session()->flash('success', 'Demande d\'absence  soumise avec succès.');
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors de la soumission de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage()]
            );

            session()->flash('error', 'Une erreur est survenue lors de la soumission de la demande d\'absence.', $th->getMessage());
        }
    }

    //--- Rappelle de la demande d'absence
    public function rapelleDemandeAbsence()
    {
        $user_connect = Auth::user();
        try {
            $comment = 'La demande a été rappelée par ' . Auth::user()->name;

            $this->demandeAbsence->applyTransition('rappeler', ['comment' => $comment, 'motif' => $this->motif]);

            $this->showRappelerModal = false;
            $this->reset('motif');

            //--- Journalisation
            Log::channel('daily')->info("La demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " vient d'être rappelée par l' utilisateur " . $user_connect->name);

            session()->flash('success', 'Demande d\'absence rappelée avec succès.');
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors du rappel de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage()]
            );

            session()->flush('error', 'Une erreur est survenue lors du rappel de la demande d\'absence.', $th->getMessage());
        }
    }

    //--- Approuver la demande d'absence
    public function approuverDemandeAbsence()
    {
        $user_connect = Auth::user();
        try {
            //--- Selection des semaines de l'année ---
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

                /* On prend la date de début la plus tardive entre la demande d'absence et la semaine courante
                Cela permet de ne pas dépasser les limites de la semaine lors du calcul  */
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
                    'annee_semaine_id' => $semaine->id,
                    'employe_id' => $this->demandeAbsence->employe_id,
                    'total_heure' => $jours_absence * $this->demandeAbsence->heure_par_jour,
                    'workflow_state' => 'valide',
                    'statut' => 'valide'
                ]);
            }

            $comment = 'La demande est approuvée par ' . Auth::user()->name;

            $this->demandeAbsence->applyTransition('valider', ['comment' => $comment]);
            $this->showApprouverModal = false;

            //--- Journalisation
            Log::channel('daily')->info("La demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " vient d'être validée par l' utilisateur " . $user_connect->name);

            session()->flash('success', 'Demande d\'absence validée avec succès.');
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors de la validation de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage()]
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

            $comment = 'La demande a été retournée par ' . Auth::user()->name;
            $this->demandeAbsence->applyTransition('retourner', ['comment' => $comment, 'motif' => $this->motif]);

            $this->showRetournerModal = false;
            $this->reset('motif');

            //--- Journalisation
            Log::channel('daily')->info("La demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " vient d'être retournée par l' utilisateur " . $user_connect->name);

            session()->flash('success', 'Demande d\'absence retournée avec succès.');
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors du retour de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage()]
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

            $comment = 'La demande a été rejetée par : ' . Auth::user()->name;
            $this->demandeAbsence->applyTransition('rejeter', ['comment' => $comment, 'motif' => $this->motif]);
            $this->showRejeterModal = false;
            $this->reset('motif');

            //--- Journalisation
            Log::channel('daily')->info("La demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " vient d'être rejetée par l' utilisateur " . $user_connect->name);

            session()->flash('success', 'Demande d\'absence rejetée avec succès.');
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors du rejet de la demande d'absence de l'employé " . $this->demandeAbsence->employe?->nom . " " . $this->demandeAbsence->employe?->prenom  . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage()]
            );

            session()->flash('error', 'Une erreur est survenue lors du rejet de la demande d\'absence.', $th->getMessage());
        }
    }

    public function render()
    {
        return view(
            'rhfeuilledetempsabsence::livewire.rh-feuille-de-temps-absence-details',
            [
                'logs' => $this->demandeAbsence->get_workflow_log()
            ]
        );
    }
}
