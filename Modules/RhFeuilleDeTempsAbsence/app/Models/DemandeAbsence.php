<?php

namespace Modules\RhFeuilleDeTempsAbsence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;
use Modules\Rh\Models\Employe\Employe;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsReguliere\Models\LigneTravail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\RhFeuilleDeTempsAbsence\Traits\HasWorkflow;

class DemandeAbsence extends Model
{
    use HasFactory, HasWorkflow;

    protected $table = 'demande_absences';

    protected $fillable = [
        'annee_financiere_id',
        'employe_id',
        'codes_travail_id',
        'date_debut',
        'date_fin',
        'heure_par_jour',
        'total_heure',
        'description',
        'workflow_log',
        'statut',
        'admin_id'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    // Relations existantes...
    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    public function codeTravail()
    {
        return $this->belongsTo(CodeTravail::class, 'codes_travail_id');
    }

    public function anneeFinanciere()
    {
        return $this->belongsTo(AnneeFinanciere::class, 'annee_financiere_id');
    }

    public function operations()
    {
        return $this->hasMany(Operation::class, 'demande_absence_id');
    }

    // ================================
    // MÉTHODES WORKFLOW SPÉCIALISÉES
    // ================================

    /**
     * Actions spécifiques lors de la validation d'une absence
     */

    protected function onValidation(array $context = []): void
    {
        // Remplir automatiquement les feuilles de temps
        $this->remplirFeuillesDeTempsAutomatiquement();

        // Créer les opérations si nécessaire
        $this->creerOperationsAbsence();
    }

    /**
     * Actions spécifiques lors du rejet
     */
    protected function onRejection(array $context = []): void
    {
        // Supprimer les remplissages automatiques existants
        $this->supprimerRemplissageAutomatique();

        // Supprimer les opérations créées
        $this->operations()->delete();
    }

    /**
     * Remplir automatiquement les feuilles de temps concernées
     */
    private function remplirFeuillesDeTempsAutomatiquement(): void
    {
        // Obtenir les semaines concernées par l'absence
        $semaines = $this->getSemainesConcernees();

        foreach ($semaines as $semaine) {
            // Obtenir ou créer l'opération pour cette semaine
            $operation = Operation::getOrCreateOperation($this->employe_id, $semaine->id);

            // Calculer les dates de début et fin pour cette semaine
            $dateDebutSemaine = $this->getDateDebutPourSemaine($semaine);
            $dateFinSemaine = $this->getDateFinPourSemaine($semaine);

            // Créer la ligne de travail auto-remplie
            $this->creerLigneTravailAbsence($operation, $dateDebutSemaine, $dateFinSemaine);
        }
    }

    /**
     * Obtenir les semaines concernées par l'absence
     */
    private function getSemainesConcernees()
    {
        return SemaineAnnee::where('annee_financiere_id', $this->annee_financiere_id)
            ->where('fin', '>=', $this->date_debut->toDateString())
            ->where('debut', '<=', $this->date_fin->toDateString())
            ->orderBy('numero_semaine')
            ->get();
    }

    /**
     * Calculer la date de début effective pour une semaine donnée
     */
    private function getDateDebutPourSemaine(SemaineAnnee $semaine): Carbon
    {
        $debutAbsence = $this->date_debut->copy();
        $debutSemaine = Carbon::parse($semaine->debut);

        // Prendre la date la plus tardive (début de semaine ou début d'absence)
        $dateDebut = $debutAbsence->gt($debutSemaine) ? $debutAbsence : $debutSemaine;

        // Si c'est un dimanche, passer au lundi
        if ($dateDebut->isSunday()) {
            $dateDebut->addDay();
        }

        return $dateDebut;
    }

    /**
     * Calculer la date de fin effective pour une semaine donnée
     */
    private function getDateFinPourSemaine(SemaineAnnee $semaine): Carbon
    {
        $finAbsence = $this->date_fin->copy();
        $finSemaine = Carbon::parse($semaine->fin);

        // Prendre la date la plus proche (fin de semaine ou fin d'absence)
        $dateFin = $finAbsence->lt($finSemaine) ? $finAbsence : $finSemaine;

        // Si c'est un samedi, passer au vendredi
        if ($dateFin->isSaturday()) {
            $dateFin->subDay();
        }

        return $dateFin;
    }

    /**
     * Créer une ligne de travail pour l'absence
     */
    private function creerLigneTravailAbsence(Operation $operation, Carbon $dateDebut, Carbon $dateFin): void
    {
        // Créer la ligne de travail
        $ligne = new LigneTravail([
            'operation_id' => $operation->id,
            'codes_travail_id' => $this->codes_travail_id,
            'auto_rempli' => true,
            'type_auto_remplissage' => 'absence',
            'demande_absence_id' => $this->id
        ]);

        // Remplir les jours concernés
        $currentDate = $dateDebut->copy();
        while ($currentDate <= $dateFin) {
            $jourSemaine = $currentDate->dayOfWeek === 0 ? 6 : $currentDate->dayOfWeek - 1; // 0=Lundi, 6=Dimanche

            // Seulement les jours ouvrables (Lundi à Vendredi)
            if ($jourSemaine >= 0 && $jourSemaine <= 4) {
                $heuresJour = min($this->heure_par_jour, 8); // Max 8h par jour

                $ligne->setJourHeures(
                    $jourSemaine,
                    '08:00',
                    sprintf('%02d:00', 8 + $heuresJour),
                    $heuresJour
                );
            }

            $currentDate->addDay();
        }

        $ligne->save();
    }

    /**
     * Créer les opérations d'absence
     */
    private function creerOperationsAbsence(): void
    {
        $semaines = $this->getSemainesConcernees();

        foreach ($semaines as $semaine) {
            // Vérifier si l'opération existe déjà
            if (!$this->operations()->where('annee_semaine_id', $semaine->id)->exists()) {
                Operation::create([
                    'demande_absence_id' => $this->id,
                    'annee_semaine_id' => $semaine->id,
                    'employe_id' => $this->employe_id,
                    'total_heure' => $this->calculerHeuresPourSemaine($semaine),
                    'workflow_state' => 'valide', // Directement validé car c'est une absence
                    'statut' => 'Validé'
                ]);
            }
        }
    }

    /**
     * Calculer les heures d'absence pour une semaine donnée
     */
    private function calculerHeuresPourSemaine(SemaineAnnee $semaine): float
    {
        $dateDebut = $this->getDateDebutPourSemaine($semaine);
        $dateFin = $this->getDateFinPourSemaine($semaine);

        $joursOuvrables = 0;
        $currentDate = $dateDebut->copy();

        while ($currentDate <= $dateFin) {
            $jourSemaine = $currentDate->dayOfWeek === 0 ? 6 : $currentDate->dayOfWeek - 1;

            // Compter seulement les jours ouvrables
            if ($jourSemaine >= 0 && $jourSemaine <= 4) {
                $joursOuvrables++;
            }

            $currentDate->addDay();
        }

        return $joursOuvrables * min($this->heure_par_jour, 8);
    }

    /**
     * Supprimer le remplissage automatique en cas de rejet
     */
    private function supprimerRemplissageAutomatique(): void
    {
        LigneTravail::where('demande_absence_id', $this->id)
            ->where('auto_rempli', true)
            ->delete();
    }

    //--- scope pour la liste des demandes d'absence d'un employé
    public function scopeEmployeConnecte($query)
    {
        $employeId = Auth::user()->employe->id;

        return $query->where('employe_id', $employeId)
            ->orWhere('admin_id', Auth::user()->id);
    }

    //--- scope pour la liste des demandes d'absence d'un gestionnaire
    public function scopeGestionnaireConnecte($query)
    {
        $employe = Auth::user()->employe;

        return $query->where(function ($q) use ($employe) {
            $q->whereHas('employe', function ($subQuery) use ($employe) {
                $subQuery->where('gestionnaire_id', $employe->id);
            })->orWhere('employe_id', $employe->id);
        })->orWhere('admin_id', Auth::user()->id);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'Soumis');
    }

    public function scopeApprouve($query)
    {
        return $query->where('statut', 'Validé');
    }
}
