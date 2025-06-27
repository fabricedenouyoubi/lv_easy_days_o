<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

use function Laravel\Prompts\select;

class RhFeuilleDeTempsAbsenceDetails extends Component
{
    use WithPagination;
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

    public $jour_feriee_list;


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


    function nombreDeJoursEntre($dateA, $dateB)
    {
        $dateDebut = $dateA instanceof Carbon
            ? $dateA->copy()->startOfDay()
            : Carbon::parse($dateA)->startOfDay();

        $dateFin = $dateB instanceof Carbon
            ? $dateB->copy()->startOfDay()
            : Carbon::parse($dateB)->startOfDay();

        return $dateDebut->diffInDays($dateFin) + 1;
    }


    //--- recuperer les jours non ouvrables pour les exclure dans le calcul des heures d'absence
    public function recupererDateNonOuvrable()
    {
        //---Recuperer les Jours feriés
        $jour_feriee_list = Configuration::whereHas('codeTravail.categorie', function ($query) {
            $query->where('intitule', 'Fermé');
        })->pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();

        //--- extraire les dimanches des demanines de l'année ---
        $sundayDates = SemaineAnnee::where('annee_financiere_id', $this->demandeAbsence->annee_financiere_id)
            ->get()
            ->map(function ($semaine) {
                // Calcule le dimanche à partir de la date de début
                $sunday = Carbon::parse($semaine->debut)->next(Carbon::SUNDAY);
                // Vérifie que ce dimanche est bien dans la semaine
                if ($sunday->between($semaine->debut, $semaine->fin)) {
                    return $sunday->toDateString(); // ou format('d/m/Y')
                }

                return null;
            })
            ->filter()
            ->values()
            ->all();

        $date = collect($jour_feriee_list)
            ->merge($sundayDates)
            ->unique() // elimine les doublons
            ->sort()   // classe par ordre croissant
            ->values() // Réindexe les clés de 0 à n-1
            ->all(); // Retourne un tableau
        return $date;
    }

    public function mount()
    {
        $this->demandeAbsence = DemandeAbsence::with('employe', 'codeTravail', 'operations.anneeSemaine')->findOrFail($this->demandeAbsenceId);
        $this->workflow_log = $this->demandeAbsence->workflow_log;
        $this->nombreJourAbsence = $this->nombreDeJoursEntre($this->demandeAbsence->date_debut, $this->demandeAbsence->date_fin);

        //dd($this->recupererDateNonOuvrable());
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
            ->filter() // élimine les lignes non valides (nulls)
            ->reverse() // <-- Tri du plus récent au plus ancien
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
            //throw $th;
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

            //--- Enregistrement des opérations
            foreach ($semaines as $semaine) {
                Operation::create([
                    'demande_absence_id' => $this->demandeAbsence->id,
                    'annee_semaine_id' => $semaine->id,
                    'employe_id' => $this->demandeAbsence->employe_id,
                    'total_heure' => $this->demandeAbsence->total_heure,
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
            //dd($th->getMessage());
        }
    }

    //--- Retourner la demande d'absence
    public function retournerDemandeAbsence()
    {
        $this->validate(['motif' => ['required', 'string', 'min:5'],]);
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
