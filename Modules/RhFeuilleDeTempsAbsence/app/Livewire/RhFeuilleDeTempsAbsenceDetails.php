<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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

    //--- Contruction du journal de la demande d'absence après modification
    public function build_workflow_log($from, $to, $comment = null)
    {
        $timestamp = now();
        $log = [
            'timestamp' => $timestamp->format('Y-m-d H:i'),
            'date' => $timestamp->format('d-m-Y'),
            'time' => $timestamp->format('H:i'),
            'from_state' => $from,
            'to_state' => $to,
            'comment' => $comment ?? '',
            'title' => $from . ' à ' . $to
        ];


        $logs = $this->workflow_log ? explode("\n", $this->workflow_log) : [];

        //--- chargement du nouveau journal de la demande d'absence
        $logs[] = json_encode($log);

        //--- mis a jour du journal de la demande d'absence
        $this->workflow_log = implode("\n", $logs);
    }

    //--- recuperation du journal de la demande d'absence
    public function get_workflow_log()
    {
        $demande = DemandeAbsence::findOrFail($this->demandeAbsenceId);
        $logs = json_decode($demande->workflow_log, true);
        $logsArray = collect(explode("\n", $demande->workflow_log))
            ->filter() // élimine les lignes vides
            ->map(fn($line) => json_decode(trim($line), true))
            ->filter()  // élimine les lignes non valides (nulls)
            ->reverse() // Tri du plus récent au plus ancien
            ->values(); // Pour réindexer proprement;
        return $logsArray;
    }

    //--- Soumission de la demande d'absence
    public function soumettreDemandeAbsence()
    {
        try {

            $comment = $this->demandeAbsence->admin_id == Auth::user()->id ? 'La demande a été soumise par ' . Auth::user()->name : 'La demande a été soumise';
            $this->build_workflow_log($this->demandeAbsence->statut, $this->statuts[2], $comment);
            $this->demandeAbsence->update([
                'statut' => $this->statuts[2],
                'workflow_log' => $this->workflow_log
            ]);
            $this->showSoumissionModal = false;
            session()->flash('success', 'Demande d\'absence  soumise avec succès.');
        } catch (\Throwable $th) {
            session()->flash('error', 'Une erreur est survenue lors de la soumission de la demande d\'absence.', $th->getMessage());
        }
    }

    //--- Rappelle de la demande d'absence
    public function rapelleDemandeAbsence()
    {
        try {
            $comment = $this->demandeAbsence->admin_id == Auth::user()->id ? 'La demande a été rappelée par ' . Auth::user()->name : 'La demande a rappelée soumise';
            $comment = $this->motif ? $comment .  ' avec pour motif :  << ' . $this->motif . ' >>' : $comment;

            $this->build_workflow_log($this->demandeAbsence->statut, $this->statuts[1], $comment);
            $this->demandeAbsence->update([
                'statut' => $this->statuts[1],
                'workflow_log' => $this->workflow_log
            ]);
            $this->showRappelerModal = false;
            $this->reset('motif');
            session()->flash('success', 'Demande d\'absence rappelée avec succès.');
        } catch (\Throwable $th) {
            //dd($th->getMessage());
        }
    }

    //--- Approuver la demande d'absence
    public function approuverDemandeAbsence()
    {
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
                    'workflow_state' => 'valide', // Directement validé car c'est une absence
                    'statut' => 'Validé'
                ]);
            }

            $this->build_workflow_log($this->demandeAbsence->statut, $this->statuts[3], 'La demande est approuvée par ' . Auth::user()->name);
            $this->demandeAbsence->update([
                'statut' => $this->statuts[3],
                'workflow_log' => $this->workflow_log
            ]);

            $this->showApprouverModal = false;
            session()->flash('success', 'Demande d\'absence validée avec succès.');
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    //--- Retourner la demande d'absence
    public function retournerDemandeAbsence()
    {
        $this->validate(['motif' => ['required', 'string', 'min:5']]);
        try {

            if ($this->demandeAbsence->operations()->count() > 0) {
                $this->demandeAbsence->operations()->delete();
            }

            $comment = 'La demande a été retournée par ' . Auth::user()->name .  ' avec pour motif :  << ' . $this->motif . ' >>';
            $this->build_workflow_log($this->demandeAbsence->statut, $this->statuts[1], $comment);
            $this->demandeAbsence->update([
                'statut' => $this->statuts[1],
                'workflow_log' => $this->workflow_log
            ]);
            $this->showRetournerModal = false;
            $this->reset('motif');
            session()->flash('success', 'Demande d\'absence retournée avec succès.');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    //--- Rejeter la demande d'absence
    public function rejeterDemandeAbsence()
    {
        $this->validate(['motif' => ['required', 'string', 'min:5'],]);
        try {
            //--- Suppresion des opérations et liaison avec la feuille de temps
            $this->demandeAbsence->operations()->delete();

            $comment = 'La demande a été rejetée par : ' . Auth::user()->name . ' avec pour motif :  << ' . $this->motif . ' >>';
            $this->build_workflow_log($this->demandeAbsence->statut, $this->statuts[4], $comment);
            $this->demandeAbsence->update([
                'statut' => $this->statuts[4],
                'workflow_log' => $this->workflow_log
            ]);
            $this->showRejeterModal = false;
            $this->reset('motif');
            session()->flash('success', 'Demande d\'absence rejetée avec succès.');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function render()
    {
        return view(
            'rhfeuilledetempsabsence::livewire.rh-feuille-de-temps-absence-details',
            [
                'logs' => $this->get_workflow_log()
            ]
        );
    }
}
