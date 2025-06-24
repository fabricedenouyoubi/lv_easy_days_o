<?php

namespace Modules\RhFeuilleDeTempsAbsence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Rh\Models\Employe\Employe;
use Modules\RhFeuilleDeTempsConfig\Models\CodeDeTravail;

// use Modules\RhFeuilleDeTempsAbsence\Database\Factories\DemandeAbsenceFactory;

class DemandeAbsence extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'workflow_log',
        'state',
        'date_debut',
        'date_fin',
        'heure_par_jour',
        'total_heure',
        'description',
        'annee_financiere_id',
        'code_de_travail_id',
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

    public function codeDeTravail()
    {
        return $this->belongsTo(CodeDeTravail::class);
    }

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // protected static function newFactory(): DemandeAbsenceFactory
    // {
    //     // return DemandeAbsenceFactory::new();
    // }

    // Accessors
    public function getFormattedNameAttribute()
    {
        return $this->employe && $this->date_debut
            ? "{$this->employe->nom} - " . $this->date_debut->format('Y-m-d')
            : "Demande d'absence #{$this->id}";
    }

    // Calcul du total des heures
    public function calculateTotalHeures()
    {
        if ($this->date_debut && $this->date_fin && $this->heure_par_jour) {
            $jours = $this->date_fin->diffInDays($this->date_debut) + 1;
            $this->total_heure = $jours * $this->heure_par_jour;
            return $this->total_heure;
        }

        return 0;
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('state','Brouillon');
    }

    public function scopeApprouve($query)
    {
        return $query->where('state', );
    }

    // Méthodes statiques
    public static function getNombreDeDemandeEnAttente()
    {
        return self::enAttente()->count();
    }

    public static function getNombreDeDemandeApprouve()
    {
        return self::approuve()->count();
    }

    public static function getProchaineAbsence()
    {
        return self::approuve()->orderBy('date_debut')->first();
    }
}
