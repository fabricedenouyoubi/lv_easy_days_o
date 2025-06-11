<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\RhEmploye\Models\Employe;

class HistoriqueGestionnaire extends Model
{
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

}
