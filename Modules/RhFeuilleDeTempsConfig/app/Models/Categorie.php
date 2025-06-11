<?php

namespace Modules\RhFeuilleDeTempsConfig\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\RhFeuilleDeTempsConfig\Database\Factories\CategorieFactory;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'intitule',
        'configurable',
        'valeur_config',
    ];

    protected $casts = [
        'configurable' => 'boolean',
    ];

    /**
     * Valeurs possibles pour valeur_config
     */
    public static function getValeurConfigOptions()
    {
        return [
            'Individuel' => 'Individuel',
            'Collectif' => 'Collectif',
            'Jour' => 'Jour',
        ];
    }

    /**
     * Scope pour filtrer par configurabilité
     */
    public function scopeConfigurable($query, $value = null)
    {
        if ($value === null) {
            return $query;
        }
        
        if ($value === 'aucun') {
            return $query->where('configurable', false);
        }
        
        return $query->where('configurable', true)->where('valeur_config', $value);
    }

    /**
     * Scope pour recherche par intitulé
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }
        
        return $query->where('intitule', 'like', '%' . $search . '%');
    }

    /**
     * Relation avec CodeTravail 
     */
    /* public function codesTravail()
    {
        return $this->hasMany(\Modules\RhFeuilleDeTempsConfig\Models\CodeTravail::class);
    } */

    // protected static function newFactory(): CategorieFactory
    // {
    //     // return CategorieFactory::new();
    // }
}
