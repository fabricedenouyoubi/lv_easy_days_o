<?php

namespace Modules\RhFeuilleDeTempsConfig\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\RhFeuilleDeTempsConfig\Database\Factories\CodeDeTravailFactory;

class CodeDeTravail extends Model
{
    use HasFactory;

    protected $table = 'codes_de_travail';

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'categorie'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Validation rules
     */
    public static function rules()
    {
        return [
            'code' => 'required|string|max:5|unique:codes_de_travail,code',
            'libelle' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'categorie' => 'required|string|in:' . implode(',', CategorieCodeDeTravail::values())
        ];
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeOfCategorie($query, $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    /**
     * Scope pour les codes d'absence
     */
    public function scopeAbsence($query)
    {
        return $query->whereIn('categorie', CategorieCodeDeTravail::getAbsenceCategories());
    }

    /**
     * Scope pour les codes de travail
     */
    public function scopeWork($query)
    {
        return $query->whereIn('categorie', CategorieCodeDeTravail::getWorkCategories());
    }

    /**
     * Accesseur pour vérifier si c'est un code d'absence
     */
    public function getIsAbsenceAttribute()
    {
        return CategorieCodeDeTravail::isAbsence($this->categorie);
    }

    /**
     * Accesseur pour le nom d'affichage
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->libelle} ({$this->code})";
    }

    /**
     * Accesseur pour le libellé de la catégorie
     */
    public function getCategorieLibelleAttribute()
    {
        return CategorieCodeDeTravail::getLabel($this->categorie);
    }

    /**
     * Relation avec les configurations de codes de travail
     */
    public function configurations()
    {
        return $this->hasMany(ConfigurationCodeDeTravail::class);
    }

    /**
     * Relation avec les lignes de travail (à créer plus tard)
     */
    public function lignesDeTravail()
    {
        return $this->hasMany('Modules\RhFeuilleDeTemps\Models\LigneDeTravail');
    }

    /**
     * Méthode pour obtenir un code par son code unique
     */
    public static function getByCode($code)
    {
        return self::where('code', $code)->first();
    }

    /**
     * Méthode pour obtenir les codes par catégorie
     */
    public static function getByCategorie($categorie)
    {
        return self::ofCategorie($categorie)->orderBy('libelle')->get();
    }

    public function __toString()
    {
        return $this->libelle;
    }

    // protected static function newFactory(): CodeDeTravailFactory
    // {
    //     // return CodeDeTravailFactory::new();
    // }
}
