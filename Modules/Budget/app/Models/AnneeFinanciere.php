<?php

namespace Modules\Budget\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
// use Modules\Budget\Database\Factories\AnneeFinanciereFactory;

class AnneeFinanciere extends Model
{
    use HasFactory;

    protected $table = 'annee_financieres';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'debut',
        'fin',
        'statut',
        'actif'
    ];

    protected $casts = [
        'debut' => 'date',
        'fin' => 'date',
        'actif' => 'boolean'
    ];

    // Definition des constantes pour les statuts
    const STATUT_ACTIF = 'ACTIF';
    const STATUT_INACTIF = 'INACTIF';

    public static function getStatuts(){
        return [
            self::STATUT_ACTIF => 'Actif',
            self::STATUT_INACTIF => 'Inactif'
        ];
    }

    // Scope pour l'annee active
    public function scopeActif($query)
    {
        return $query->where('statut', self::STATUT_ACTIF)->where('actif', true);
    }

    // Scope pour l'annee inactives
    public function scopeInactif($query)
    {
        return $query->where('statut', self::STATUT_INACTIF)->orWhere('actif', false);
    }

}
