<?php

namespace Modules\Rh\Models\Employe;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\Rh\Database\Factories\Employe/HistoriqueGestionnaireFactory;

class HistoriqueGestionnaire extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
        protected $table = 'historique_gestionnaires';

    protected $fillable = [
        'employe_id',
        'gestionnaire_id',
        'date_debut',
        'date_fin',
    ];

    protected $dates = [
        'date_debut',
        'date_fin',
    ];

    // Relations
    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    public function gestionnaire(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'gestionnaire_id');
    }

    // protected static function newFactory(): Employe/HistoriqueGestionnaireFactory
    // {
    //     // return Employe/HistoriqueGestionnaireFactory::new();
    // }
}
