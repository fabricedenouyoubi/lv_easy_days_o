<?php

namespace Modules\RhFeuilleDeTempsConfig\Models\Comportement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Rh\Models\Employe\Employe;

// use Modules\RhFeuilleDeTempsConfig\Database\Factories\Comportement/ConfigurationFactory;

class Configuration extends Model
 {
    use HasFactory;

    protected $fillable = [
        'libelle',
        'quota',
        'consomme',
        'reste',
        'date',
        'commentaire',
        'employe_id',
        'annee_budgetaire_id',
        'code_travail_id',
    ];

    protected $casts = [
        'quota' => 'decimal:2',
        'consomme' => 'decimal:2',
        'reste' => 'decimal:2',
        'date' => 'date',
    ];

    /**
     * Relation avec Employe
     */
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    /**
     * Relation avec AnneeBudgetaire
     */
    public function anneeBudgetaire()
    {
        return $this->belongsTo(AnneeFinanciere::class, 'annee_budgetaire_id');
    }

    /**
     * Relation avec CodeTravail
     */
    public function codeTravail()
    {
        return $this->belongsTo(\Modules\RhFeuilleDeTempsConfig\Models\CodeTravail::class);
    }

    /**
     * Scope pour les jours fériés (pas d'employé spécifique)
     */
    public function scopeJoursFeries($query)
    {
        return $query->whereNull('employe_id')
                    ->whereNotNull('date');
    }

    /**
     * Scope pour une année budgétaire spécifique
     */
    public function scopeForAnnee($query, $anneeBudgetaireId)
    {
        return $query->where('annee_budgetaire_id', $anneeBudgetaireId);
    }

    /**
     * Scope pour un code de travail spécifique
     */
    public function scopeForCodeTravail($query, $codeTravailId)
    {
        return $query->where('code_travail_id', $codeTravailId);
    }

    /**
     * Scope pour recherche par libellé
     */
    public function scopeSearchByLibelle($query, $search)
    {
        if (empty($search)) {
            return $query;
        }
        
        return $query->where('libelle', 'like', '%' . $search . '%');
    }

    /**
     * Vérifier si c'est un jour férié
     */
    public function isJourFerie()
    {
        return is_null($this->employe_id) && !is_null($this->date);
    }

    /**
     * Formatter la date pour l'affichage
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('d M. Y') : null;
    }

    /**
     * Obtenir le jour de la semaine en français
     */
    public function getJourSemaineAttribute()
    {
        if (!$this->date) return null;
        
        $jours = [
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi', 
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
            'Sunday' => 'Dimanche'
        ];
        
        return $jours[$this->date->format('l')] ?? $this->date->format('l');
    }

    /**
     * Relation Many-to-Many avec Employe (pour comportement Collectif)
     */
    public function employes()
    {
        return $this->belongsToMany(Employe::class, 'configuration_employe')
                    ->withPivot('consomme_individuel')
                    ->withTimestamps();
    }

    /**
     * Scope pour les configurations collectives
     */
    public function scopeCollectif($query)
    {
        return $query->whereNull('employe_id')
                    ->whereNull('date');
    }

    /**
     * Vérifier si c'est une configuration collective
     */
    public function isCollectif()
    {
        return is_null($this->employe_id) && is_null($this->date);
    }

    /**
     * Obtenir le total des heures consommées individuellement (pour collectif)
     */
    public function getTotalConsommeIndividuelAttribute()
    {
        return $this->employes()->sum('configuration_employe.consomme_individuel');
    }

    /**
     * Obtenir le nombre d'employés affectés
     */
    public function getNombreEmployesAffectesAttribute()
    {
        return $this->employes()->count();
    }
}