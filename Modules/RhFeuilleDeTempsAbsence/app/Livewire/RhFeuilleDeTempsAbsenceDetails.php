<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;

use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;

class RhFeuilleDeTempsAbsenceDetails extends Component
{
    use WithPagination;
    public $demandeAbsenceId;
    public $demandeAbsence;
    public $nombreJourAbsence;
    public $workflow_log;

    public $showEditAbsenceModal = false;
    public $showSoumissionModal = false;
    public $showRappelerModal = false;
    public $showApprouverModal = false;
    public $showRetournerModal = false;
    public $showRejeterModal = false;



    public $status = [
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

    public function mount()
    {
        $this->demandeAbsence = DemandeAbsence::with('employe', 'codeTravail')->findOrFail($this->demandeAbsenceId);
        $this->workflow_log = $this->demandeAbsence->workflow_log;
        $this->nombreJourAbsence = $this->nombreDeJoursEntre($this->demandeAbsence->date_debut, $this->demandeAbsence->date_fin);
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
            ->map(fn($line) => json_decode(trim($line), true))
            ->filter()
            ->sortByDesc('timestamp') // <-- Tri du plus récent au plus ancien
            ->values(); // Pour réindexer proprement;
        return $logsArray;
    }

    //--- Soumission de la demande d'absence
    public function soumettreDemandeAbsence()
    {
        try {
            $this->build_workflow_log($this->status[1], $this->status[2], 'La demande est en cours d\'approbation');
            $this->demandeAbsence->update([
                'status' => $this->status[2],
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
            $this->build_workflow_log($this->status[2], $this->status[1], 'La demande est en cours de redaction');
            $this->demandeAbsence->update([
                'status' => $this->status[1],
                'workflow_log' => $this->workflow_log
            ]);
            $this->showRappelerModal = false;
            session()->flash('success', 'Demande d\'absence rappelée avec succès.');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    //--- Approuver la demande d'absence
    public function approuverDemandeAbsence()
    {
        try {
            $this->build_workflow_log($this->status[2], $this->status[3], 'La demande est approuvée');
            $this->demandeAbsence->update([
                'status' => $this->status[3],
                'workflow_log' => $this->workflow_log
            ]);
            $this->showApprouverModal = false;
            session()->flash('success', 'Demande d\'absence validée avec succès.');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    //--- Retourner la demande d'absence
    public function retournerDemandeAbsence()
    {
        try {
            $this->build_workflow_log($this->status[3], $this->status[1], 'La demande est en cours de redaction');
            $this->demandeAbsence->update([
                'status' => $this->status[1],
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
        try {
            $this->build_workflow_log($this->demandeAbsence->status, $this->status[4], 'La demande  rejetée');
            $this->demandeAbsence->update([
                'status' => $this->status[4],
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
