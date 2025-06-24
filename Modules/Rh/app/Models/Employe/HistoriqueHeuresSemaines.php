<?php

namespace Modules\Rh\Models\Employe;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\Rh\Database\Factories\Employe/HistoriqueHeuresSemainesFactory;

class HistoriqueHeuresSemaines extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employe_id',
        'nombre_d_heure_semaine',
        'date_debut',
        'date_fin',
    ];

    protected $dates = [
        'date_debut',
        'date_fin',
    ];

    // protected static function newFactory(): HistoriqueHeuresSemainesFactory
    // {
    //     // return HistoriqueHeuresSemainesFactory::new();
    // }

    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }
}
