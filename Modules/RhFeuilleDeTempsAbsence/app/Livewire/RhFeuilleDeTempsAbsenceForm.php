<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Rh\Models\Employe\Employe;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;

class RhFeuilleDeTempsAbsenceForm extends Component
{
    public $demande_absence_id;
    public $workflow_log;
    public $date_debut;
    public $date_fin;
    public $heure_par_jour;
    public $description;
    public $code_de_travail_id;
    public $type_absence_list;

    public $status = [
        'Brouillon',
        'En cours',
        'Soumis',
        'Validé',
        'Rejeté'
    ];

    public function mount()
    {
        try {
            //--- Selection des categories conerné par les types d'absences(Code de traveil)
            $categorieIds  = Categorie::whereIn('intitule', ['Absence', 'Caisse'])->pluck('id');
            //--- selection des types d'absence (Code de travail)
            $this->type_absence_list = CodeTravail::with('categorie')->whereIn('categorie_id', $categorieIds)->get();


            //--- chargement pour la modification
            if ($this->demande_absence_id) {
                $demande = DemandeAbsence::findOrFail($this->demande_absence_id);
                $this->date_debut = $demande->date_debut->toDateTimeString();
                $this->date_fin = $demande->date_fin->toDateTimeString();
                $this->heure_par_jour = $demande->heure_par_jour;
                $this->description = $demande->description;
                $this->code_de_travail_id = $demande->codes_travail_id;
            }
        } catch (\Throwable $th) {
            //--- dd($th->getMessage());
        }
    }

    protected $rules = [
        'date_debut' => 'required|date|after_or_equal:today',
        'date_fin' => 'required|date|after:date_debut',
        'heure_par_jour' => 'required|numeric|min:1|max:24',
        'description' => 'nullable|string|max:1000',
        'code_de_travail_id' => 'required|exists:codes_travail,id',
    ];

    protected $messages = [
        'date_debut.required' => 'La date de début est obligatoire.',
        'date_debut.date' => 'La date de début doit être une date valide.',

        'date_fin.required' => 'La date de fin est obligatoire.',
        'date_fin.date' => 'La date de fin doit être une date valide.',
        'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
        'date_debut.after_or_equal' => 'La date de début doit être aujourd\'hui ou une date future.',

        'heure_par_jour.required' => "L'heure par jour est obligatoire.",
        'heure_par_jour.numeric' => "L'heure par jour doit être un nombre.",
        'heure_par_jour.min' => "L'heure par jour doit être au moins :min.",
        'heure_par_jour.max' => "L'heure par jour ne peut pas dépasser :max.",

        'description.string' => 'La description doit être une chaîne de caractères.',
        'description.max' => 'La description ne peut pas dépasser :max caractères.',

        'code_de_travail_id.exists' => 'Le code de travail sélectionné est invalide.',
        'code_de_travail_id.required' => 'Le type d\'absence est obligatoire.'
    ];

    //--- Contruction du journal de la demande d'absence
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

    //--- calcul la l'heure total de l'absence
    public function calculateTotalHeures()
    {
        if ($this->date_debut && $this->date_fin && $this->heure_par_jour) {
            $dateDebut = $this->date_debut instanceof Carbon
                ? $this->date_debut->copy()->startOfDay()
                : Carbon::parse($this->date_debut)->startOfDay();

            $dateFin = $this->date_fin instanceof Carbon
                ? $this->date_fin->copy()->startOfDay()
                : Carbon::parse($this->date_fin)->startOfDay();

            $jours = $dateDebut->diffInDays($dateFin) + 1;
            return $jours * $this->heure_par_jour;
        }

        return 0;
    }

    //--- Calcul du nombre de jour d'absence
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

    //--- ajout et modification d'une demande d'absence
    public function save()
    {
        $this->validate();

        try {
            $annee_financiere_id = AnneeFinanciere::where('actif', true)->first()->id;
            $employe_id = Employe::where('user_id', Auth::user()->id)->first()->id;
            $this->build_workflow_log($this->status[0], $this->status[1], 'La demande est en cours de redaction');

            if (!$this->demande_absence_id) {
                //dd($this->code_de_travail_id);
                $demande_absence = DemandeAbsence::create(
                    [
                        'annee_financiere_id' => $annee_financiere_id,
                        'employe_id' => $employe_id,
                        'codes_travail_id' => $this->code_de_travail_id,
                        'date_debut' => $this->date_debut,
                        'date_fin' => $this->date_fin,
                        'heure_par_jour' => $this->heure_par_jour,
                        'total_heure' => $this->calculateTotalHeures(),
                        'description' => $this->description,
                        'workflow_log' => $this->workflow_log,
                        'status' => $this->status[1],
                    ]
                );
                $this->dispatch('demandeAbsenceAjoute');
            } else {
                $demande_absence = DemandeAbsence::findOrFail($this->demande_absence_id);
                $demande_absence->update([
                    'employe_id' => $employe_id,
                    'codes_travail_id' => $this->code_de_travail_id,
                    'date_debut' => $this->date_debut,
                    'date_fin' => $this->date_fin,
                    'heure_par_jour' => $this->heure_par_jour,
                    'total_heure' => $this->calculateTotalHeures(),
                    'description' => $this->description,
                    'workflow_log' => $this->workflow_log,
                    'status' => $this->status[1],
                ]);

                $nombreDeJoursEntre = $this->nombreDeJoursEntre($this->date_debut, $this->date_fin);

                $this->dispatch('nombreDeJoursEntre', $nombreDeJoursEntre);
                $this->dispatch('demandeAbsenceModifie');
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    //--- vider les champs
    public function cancel()
    {
        $this->reset(['date_debut', 'date_fin', 'heure_par_jour', 'description', 'code_de_travail_id']);
    }

    //--- reinitiliaser les valeur de modification de depart
    public function resetAll()
    {
        if ($this->demande_absence_id) {
            $demande = DemandeAbsence::findOrFail($this->demande_absence_id);
            $this->date_debut = $demande->date_debut->toDateTimeString();
            $this->date_fin = $demande->date_fin->toDateTimeString();
            $this->heure_par_jour = $demande->heure_par_jour;
            $this->description = $demande->description;
            $this->code_de_travail_id = $demande->codes_travail_id;
        }
    }


    public function render()
    {
        return view('rhfeuilledetempsabsence::livewire.rh-feuille-de-temps-absence-form');
    }
}
