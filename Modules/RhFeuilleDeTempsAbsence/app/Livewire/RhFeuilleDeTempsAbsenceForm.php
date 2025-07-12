<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Modules\RhFeuilleDeTempsAbsence\Traits\AbsenceResource;
use Modules\RhFeuilleDeTempsAbsence\Workflows\DemandeAbsenceWorkflow;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Workflow\WorkflowStub;

class RhFeuilleDeTempsAbsenceForm extends Component
{
    use AbsenceResource;

    public $demande_absence_id;
    public $workflow_log;
    public $date_debut;
    public $date_fin;
    public $heure_par_jour;
    public $description;
    public $code_de_travail_id;
    public $type_absence_list;
    public $annee_financiere_id;
    public $employeId;

    public $statuts = [
        'Brouillon',
        'En cours',
        'Soumis',
        'Validé',
        'Rejeté'
    ];

    public function mount()
    {
        try {
            //--- Selection des categories conerné par les types d'absences (Code de traveil) ---
            $this->type_absence_list = CodeTravail::with('categorie')
                ->whereHas('categorie', function ($query) {
                    $query->whereIn('intitule', ['Absence', 'Caisse']);
                })
                ->get();

            //--- recuperation de l'annee financiere en cours
            $this->annee_financiere_id = AnneeFinanciere::where('actif', true)->first()->id;


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

    //--- ajout et modification d'une demande d'absence
    public function save()
    {
        $this->validate();
        $user_connect = Auth::user();

        try {

            //--- Vérification de l'existances des semaines de l'annee avec validation de la demande
            $nbrSemaine = SemaineAnnee::where('annee_financiere_id', $this->annee_financiere_id)
                ->count();

            if ($nbrSemaine == 0) {
                session()->flash('error', "Aucune semaine trouvée pour l'année en cours");
                return ;
            }

            $comment = $this->employeId ? 'La demande est en cours de redaction par ' . $user_connect?->name : 'La demande est en cours de redaction';
            if (!$this->demande_absence_id) {

                $insertData =
                    [
                        'annee_financiere_id' => $this->annee_financiere_id,
                        'employe_id' => $this->employeId ?? Auth::user()->employe?->id,
                        'codes_travail_id' => $this->code_de_travail_id,
                        'date_debut' => $this->date_debut,
                        'date_fin' => $this->date_fin,
                        'heure_par_jour' => $this->heure_par_jour,
                        'total_heure' => $this->calculateTotalHeures($this->date_debut, $this->date_fin, $this->heure_par_jour, $this->annee_financiere_id),
                        'description' => $this->description,
                        'admin_id' => $this->employeId != null ? Auth::user()->id : null
                    ];

                //--- Workflow ---
                try {

                    $workflow = WorkflowStub::make(DemandeAbsenceWorkflow::class);
                    $demande = null;
                    $workflow->start($demande, 'enregistrer', ['comment' => $comment], $insertData);

                    while ($workflow->running());

                    //--- Journalisation
                    Log::channel('daily')->info("La demande d'absence de l'employé ID :  " . $this->employeId . " vient d'être ajoutée par l' utilisateur " . $user_connect->name);

                    if ($workflow->failed()) {
                        $this->dispatch('demandeAbsenceAjouteWorkflowError');
                    } else {
                        $this->dispatch('demandeAbsenceAjoute');
                    }
                } catch (\Throwable $th) {

                    Log::channel('daily')->error("Erreur lors du lancement du workflow de creation d'une demande d'absence", ['data' => $insertData]);
                    $this->dispatch('demandeAbsenceAjouteWorkflowError');
                }
            } else {
                $demandeAbsence = DemandeAbsence::findOrFail($this->demande_absence_id);
                $demandeAbsence->update([
                    'annee_financiere_id' => $this->annee_financiere_id,
                    'codes_travail_id' => $this->code_de_travail_id,
                    'date_debut' => $this->date_debut,
                    'date_fin' => $this->date_fin,
                    'heure_par_jour' => $this->heure_par_jour,
                    'total_heure' => $this->calculateTotalHeures($this->date_debut, $this->date_fin, $this->heure_par_jour, $this->annee_financiere_id),
                    'description' => $this->description,
                ]);

                $nombreDeJoursEntre = $this->nombreDeJoursEntre($this->date_debut, $this->date_fin, $this->annee_financiere_id);

                //--- Journalisation
                Log::channel('daily')->info("La demande d'absence de l'employé " . $demandeAbsence->employe?->nom . " " . $demandeAbsence->employe?->prenom . " vient d'être modifiée par l' utilisateur " . $user_connect->name);


                $this->dispatch('nombreDeJoursEntre', $nombreDeJoursEntre);
                $this->dispatch('demandeAbsenceModifie');
            }
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors de la sauvegarde de la demande d'absence de l'employé par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage()]
            );

            session()->flash('error', 'Une erreur est survenue lors de la sauvegarde de la demande d\'absence.', $th->getMessage());
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
