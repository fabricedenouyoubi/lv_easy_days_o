<?php

namespace Modules\RhFeuilleDeTempsAbsence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Budget\Models\SemaineAnnee;
use Modules\Rh\Models\Employe\Employe;

// use Modules\RhFeuilleDeTempsAbsence\Database\Factories\OperationFactory;

class Operation extends Model
{
    use HasFactory;

    protected $table = 'operations';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'workflow_log',
        'statut',
        'total_heure',
        'total_heure_deplacement',
        'total_heure_regulier',
        'total_heure_supp',
        'total_heure_supp_ajuster',
        'total_heure_formation',
        'total_heure_sup_a_payer',
        'total_heure_csn',
        'total_heure_caisse',
        'total_heure_conge_mobile',
        'demande_absence_id',
        'employe_id',
        'annee_semaine_id',
    ];

    //--- relation avec demande d'absence
    public function demandeAbsence()
    {
        return $this->belongsTo(DemandeAbsence::class, 'demande_absence_id');
    }

    //--- relation avec employe
    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    //--- relation avec annee semaine
    public function anneeSemaine()
    {
        return $this->belongsTo(SemaineAnnee::class, 'annee_semaine_id');
    }

    // protected static function newFactory(): OperationFactory
    // {
    //     // return OperationFactory::new();
    // }
}
