<?php

namespace Modules\RhFeuilleDeTempsAbsence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        return $query->where('satus', 'En cours');
    }

    public function scopeApprouve($query)
    {
        return $query->where('status', 'Validé');
    }

    public static function getProchaineAbsence()
    {
        return self::approuve()->orderBy('date_debut')->first();
    }
}
