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
        'status',
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

    public function anneeFinanciere()
    {
        return $this->belongsTo(AnneeFinanciere::class);
    }

    public function codeTravail()
    {
        return $this->belongsTo(CodeTravail::class, 'codes_travail_id');
    }

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // protected static function newFactory(): DemandeAbsenceFactory
    // {
    //     // return DemandeAbsenceFactory::new();
    // }


    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('status', 'En cours');
    }

    public function scopeApprouve($query)
    {
        return $query->where('status', 'Validé');
    }

    public static function getProchaineAbsence()
    {
        return self::approuve()->orderBy('date_debut')->first();
    }

    public function scopeGestionnaireConnecte($query)
    {
        $employe = Auth::user()->employe;

        return $query->where(function ($q) use ($employe) {
            $q->whereHas('employe', function ($subQuery) use ($employe) {
                $subQuery->where('gestionnaire_id', $employe->id);
            })->orWhere('employe_id', $employe->id);
        })->orWhere('admin_id', Auth::user()->id);
    }

    public function scopeEmployeConnecte($query)
    {
        $employeId = Auth::user()->employe->id;

        return $query->where('employe_id', $employeId)
            ->orWhere('admin_id', Auth::user()->id);
    }
}
