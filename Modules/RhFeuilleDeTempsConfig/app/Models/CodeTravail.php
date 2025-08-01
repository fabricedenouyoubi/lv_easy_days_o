<?php

namespace Modules\RhFeuilleDeTempsConfig\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

// use Modules\RhFeuilleDeTempsConfig\Database\Factories\CodeTravailFactory;

class CodeTravail extends Model
{
    use HasFactory;

    protected $table = 'codes_travail';

    protected $fillable = [
        'code',
        'libelle',
        'categorie_id',
        'est_ajustable',
        'est_banque',
        'cumule_banque',
        'est_deductible',
    ];

    /**
     * Relation avec Categorie
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    /**
     * Vérifier si le code de travail est configurable
     */
    public function isConfigurable()
    {
        return $this->categorie && $this->categorie->configurable;
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeByCategorie($query, $categorieId = null)
    {
        if ($categorieId) {
            return $query->where('categorie_id', $categorieId);
        }
        return $query;
    }

    /**
     * Scope pour filtrer les codes configurables pour le calcul d'heures
     */
    public function scopeConfigurablePourCalcul($query)
    {
        return $query->where('est_ajustable', true);
    }
    /**
     * Scope pour recherche par code ou libellé
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', '%' . $search . '%')
                ->orWhere('libelle', 'like', '%' . $search . '%');
        });
    }

    /**
     * Scope pour recherche par code spécifiquement
     */
    public function scopeSearchByCode($query, $code)
    {
        if (empty($code)) {
            return $query;
        }

        return $query->where('code', 'like', '%' . $code . '%');
    }

    /**
     * Scope pour recherche par libellé spécifiquement
     */
    public function scopeSearchByLibelle($query, $libelle)
    {
        if (empty($libelle)) {
            return $query;
        }

        return $query->where('libelle', 'like', '%' . $libelle . '%');
    }

    /**
     * Scope pour filtrer les codes qui entrent dans la banque de temps
     */
    public function scopeEstBanque($query, $estBanque = true)
    {
        return $query->where('est_banque', $estBanque);
    }

    /**
     * Scope pour filtrer les codes qui cumulent la banque
     */
    public function scopeCumuleBanque($query, $cumuleBanque = true)
    {
        return $query->where('cumule_banque', $cumuleBanque);
    }

    /**
     * Scope pour filtrer les codes déductibles
     */
    public function scopeEstDeductible($query, $estDeductible = true)
    {
        return $query->where('est_deductible', $estDeductible);
    }
    /**
     * Relation avec Configuration
     */
    public function configurations()
    {
        return $this->hasMany(Configuration::class);
    }
}
