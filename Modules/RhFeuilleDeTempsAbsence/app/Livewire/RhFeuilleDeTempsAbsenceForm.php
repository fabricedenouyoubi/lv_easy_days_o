<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;
use Modules\Rh\Models\Employe\Employe;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

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
    public $annee_financiere_id;
    public $employeId;

    public $statuts = [
        'Brouillon',
        'En cours',
        'Soumis',
        'Validé',
        'Rejeté'
    ];

    //--- recuperer les jours non ouvrables pour les exclure dans le calcul des heures d'absence
    public function recupererDateNonOuvrable($idAnneFinnEncours)
    {
        //---Recuperer les Jours feriés
        $jour_feriee_list = Configuration::whereHas('codeTravail.categorie', function ($query) {
            $query->where('intitule', 'Fermé');
        })->pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();

        //--- extraire les dimanches des demanines de l'année ---
        $sundayDates = SemaineAnnee::where('annee_financiere_id', $idAnneFinnEncours)
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

            dd($this->jour_non_ouvrable_list);
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
    public function calculateTotalHeures(): float|int
    {
        // Vérifie que toutes les données nécessaires sont bien présentes
        if (!$this->date_debut || !$this->date_fin || !$this->heure_par_jour) {
            return 0;
        }

        // Conversion des dates en objets Carbon, en commençant au début de la journée
        $dateDebut = $this->date_debut instanceof Carbon
            ? $this->date_debut->copy()->startOfDay()
            : Carbon::parse($this->date_debut)->startOfDay();

        $dateFin = $this->date_fin instanceof Carbon
            ? $this->date_fin->copy()->startOfDay()
            : Carbon::parse($this->date_fin)->startOfDay();

        //--- recuperation des jours non ouvrables
        $jour_non_ouvrable_list = $this->recupererDateNonOuvrable($this->annee_financiere_id);

        // On prépare les jours non ouvrables sous forme de tableau associatif pour un accès rapide avec isset()
        // Chaque date est convertie au format 'Y-m-d'
        $joursNonOuvrables = array_flip(
            array_map(fn($date) => Carbon::parse($date)->toDateString(), $jour_non_ouvrable_list)
        );

        // Création d'une collection de tous les jours entre dateDebut et dateFin inclus
        $totalJoursOuvrables = collect(Carbon::parse($dateDebut)->daysUntil($dateFin->copy()))
            // Exclut les jours qui sont dans la liste des jours non ouvrables
            ->reject(fn(Carbon $date) => isset($joursNonOuvrables[$date->toDateString()]))
            ->count(); // Compte les jours restants (ouvrables)

        // On multiplie le nombre de jours ouvrables par le nombre d'heures par jour pour obtenir le total
        return $totalJoursOuvrables * $this->heure_par_jour;
    }

    //--- Calcul du nombre de jour d'absence en tenant compte des jour non ouvrables
    function nombreDeJoursEntre($dateA, $dateB): int
    {
        // Conversion des dates en objets Carbon (début de journée)
        $dateDebut = $dateA instanceof Carbon
            ? $dateA->copy()->startOfDay()
            : Carbon::parse($dateA)->startOfDay();

        $dateFin = $dateB instanceof Carbon
            ? $dateB->copy()->startOfDay()
            : Carbon::parse($dateB)->startOfDay();

        //--- recuperation des jours non ouvrables
        $jour_non_ouvrable_list = $this->recupererDateNonOuvrable($this->annee_financiere_id);

        // Normalisation des jours non ouvrables au format 'Y-m-d' avec array_flip pour accès rapide
        $joursNonOuvrables = array_flip(
            array_map(fn($date) => Carbon::parse($date)->toDateString(), $jour_non_ouvrable_list)
        );

        // Génère la liste des jours entre les deux dates incluses
        $joursOuvrables = collect(Carbon::parse($dateDebut)->daysUntil($dateFin->copy()))
            // Supprime les jours non ouvrables
            ->reject(fn(Carbon $date) => isset($joursNonOuvrables[$date->toDateString()]))
            ->count();

        return $joursOuvrables;
    }

    //--- ajout et modification d'une demande d'absence
    public function save()
    {
        $this->validate();

        try {
            $this->build_workflow_log($this->statuts[0], $this->statuts[1], 'La demande est en cours de redaction');

            if (!$this->demande_absence_id) {
                //dd($this->code_de_travail_id);
                $demande_absence = DemandeAbsence::create(
                    [
                        'annee_financiere_id' => $this->annee_financiere_id,
                        'employe_id' => $this->employeId ?? Auth::user()->employe->id,
                        'codes_travail_id' => $this->code_de_travail_id,
                        'date_debut' => $this->date_debut,
                        'date_fin' => $this->date_fin,
                        'heure_par_jour' => $this->heure_par_jour,
                        'total_heure' => $this->calculateTotalHeures(),
                        'description' => $this->description,
                        'workflow_log' => $this->workflow_log,
                        'statut' => $this->statuts[1],
                        'admin_id' => Auth::user()->id ?? null
                    ]
                );
                $this->dispatch('demandeAbsenceAjoute');
            } else {
                $demande_absence = DemandeAbsence::findOrFail($this->demande_absence_id);
                $demande_absence->update([
                    'employe_id' => Auth::user()->employe->id,
                    'codes_travail_id' => $this->code_de_travail_id,
                    'date_debut' => $this->date_debut,
                    'date_fin' => $this->date_fin,
                    'heure_par_jour' => $this->heure_par_jour,
                    'total_heure' => $this->calculateTotalHeures(),
                    'description' => $this->description,
                    //'workflow_log' => $this->workflow_log,
                    'statut' => $this->statuts[1],
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
