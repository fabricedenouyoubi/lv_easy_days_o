<?php

namespace Modules\RhFeuilleDeTempsAbsence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Rh\Models\Employe\Employe;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;

// use Modules\RhFeuilleDeTempsAbsence\Database\Factories\DemandeAbsenceFactory;

class DemandeAbsence extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'workflow_log',
        'statut',
        'date_debut',
        'date_fin',
        'heure_par_jour',
        'total_heure',
        'description',
        'annee_financiere_id',
        'codes_travail_id',
        'employe_id',
        'admin_id'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    //--- relation avec annee financiere
    public function anneeFinanciere()
    {
        return $this->belongsTo(AnneeFinanciere::class);
    }

    //--- relation avec code de travail
    public function codeTravail()
    {
        return $this->belongsTo(CodeTravail::class, 'codes_travail_id');
    }

    //--- relation avec employe
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // protected static function newFactory(): DemandeAbsenceFactory
    // {
    //     // return DemandeAbsenceFactory::new();
    // }

    //--- relation avec operations
    public function operations()
    {
        return $this->hasMany(Operation::class, 'demande_absence_id');
    }

    //--- scope pour les absences en cours
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'En cours');
    }

    //--- scope pour les absences approuvé
    public function scopeApprouve($query)
    {
        return $query->where('statut', 'Validé');
    }

    //--- scope pour les absences cloturées
    public static function getProchaineAbsence()
    {
        return self::approuve()->orderBy('date_debut')->first();
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

    //--- scope pour la liste des demandes d'absence d'un employé
    public function scopeEmployeConnecte($query)
    {
        $employeId = Auth::user()->employe->id;

        return $query->where('employe_id', $employeId)
            ->orWhere('admin_id', Auth::user()->id);
    }
}
