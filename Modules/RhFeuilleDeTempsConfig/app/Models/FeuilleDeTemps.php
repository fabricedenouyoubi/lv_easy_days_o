<?php

namespace Modules\RhFeuilleDeTempsConfig\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Budget\Models\AnneeFinanciere;

// use Modules\RhFeuilleDeTempsConfig\Database\Factories\FeuilleDeTempsFactory;

class FeuilleDeTemps extends Model
{
    use HasFactory;

    protected $table = 'feuilles_de_temps';

    protected $fillable = [
        'numero_semaine',
        'debut',
        'fin',
        'annee_financiere_id',
        'actif',
        'est_semaine_de_paie'
    ];

    protected $casts = [
        'debut' => 'date',
        'fin' => 'date',
        'actif' => 'boolean',
        'est_semaine_de_paie' => 'boolean'
    ];

    /**
     * Relation avec l'année financière
     */
    public function anneeFinanciere()
    {
        return $this->belongsTo(AnneeFinanciere::class);
    }

    /**
     * Relation avec les opérations (à créer plus tard)
     */
    public function operations()
    {
        return $this->hasMany('Modules\RhFeuilleDeTemps\Models\Operation');
    }

    /**
     * Scope pour les feuilles actives
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les semaines de paie
     */
    public function scopeSemaineDePaie($query)
    {
        return $query->where('est_semaine_de_paie', true);
    }

    /**
     * Scope pour une année financière spécifique
     */
    public function scopeParAnneeFinanciere($query, $anneeFinanciereId)
    {
        return $query->where('annee_financiere_id', $anneeFinanciereId);
    }

    /**
     * Scope pour une plage de dates
     */
    public function scopePeriode($query, $dateDebut, $dateFin)
    {
        return $query->where('debut', '>=', $dateDebut)
                    ->where('fin', '<=', $dateFin);
    }

    /**
     * Accesseur pour la période formatée
     */
    public function getPeriodeAttribute()
    {
        return $this->debut->format('d/m/Y') . ' - ' . $this->fin->format('d/m/Y');
    }

    /**
     * Accesseur pour le libellé complet
     */
    public function getLibelleAttribute()
    {
        return "Semaine {$this->numero_semaine} ({$this->debut->format('d/m/Y')} au {$this->fin->format('d/m/Y')})";
    }

    /**
     * Vérifier si la feuille est dans une année financière active
     */
    public function isInActiveYear()
    {
        return $this->anneeFinanciere && $this->anneeFinanciere->actif;
    }

    /**
     * Obtenir la feuille de temps pour une semaine spécifique
     */
    public static function getForWeek($numeroSemaine, $anneeFinanciereId)
    {
        return self::where('numero_semaine', $numeroSemaine)
                   ->where('annee_financiere_id', $anneeFinanciereId)
                   ->first();
    }

    /**
     * Obtenir la feuille de temps pour une date donnée
     */
    public static function getForDate($date, $anneeFinanciereId = null)
    {
        $query = self::where('debut', '<=', $date)
                    ->where('fin', '>=', $date);

        if ($anneeFinanciereId) {
            $query->where('annee_financiere_id', $anneeFinanciereId);
        }

        return $query->first();
    }

    /**
     * Obtenir toutes les feuilles d'une année financière ordonnées
     */
    public static function getByAnneeFinanciere($anneeFinanciereId)
    {
        return self::parAnneeFinanciere($anneeFinanciereId)
                   ->orderBy('numero_semaine')
                   ->get();
    }

    /**
     * Compter les feuilles actives pour une année
     */
    public static function countActivesForAnnee($anneeFinanciereId)
    {
        return self::parAnneeFinanciere($anneeFinanciereId)
                   ->actif()
                   ->count();
    }

    /**
     * Manager personnalisé pour filtrer par année financière
     */
    public function newQuery()
    {
        $query = parent::newQuery();
        
        // Ajouter automatiquement l'année financière active si pas spécifiée
        if (session()->has('annee_financiere_id')) {
            $query->where('annee_financiere_id', session('annee_financiere_id'));
        }
        
        return $query;
    }

    public function __toString()
    {
        return $this->libelle;
    }

    // protected static function newFactory(): FeuilleDeTempsFactory
    // {
    //     // return FeuilleDeTempsFactory::new();
    // }
}
