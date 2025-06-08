<?php

namespace Modules\RhFeuilleDeTempsConfig\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Budget\Models\AnneeFinanciere;

// use Modules\RhFeuilleDeTempsConfig\Database\Factories\ConfigurationCodeDeTravailFactory;

class ConfigurationCodeDeTravail extends Model
{
    use HasFactory;

    protected $table = 'configuration_codes_de_travail';

    protected $fillable = [
        'libelle',
        'code_de_travail_id',
        'annee_financiere_id',
        'nombre_d_heure',
        'jour',
        'nombre_d_heure_restant',
        'nombre_d_heure_pris',
        'solde_heure_annee_precedente',
        'quantite_heure_annee_courante',
        'employe_id',
        'description',
        'debut',
        'fin'
    ];

    protected $casts = [
        'jour' => 'date',
        'debut' => 'date',
        'fin' => 'date',
        'nombre_d_heure' => 'decimal:2',
        'nombre_d_heure_restant' => 'decimal:2',
        'nombre_d_heure_pris' => 'decimal:2',
        'solde_heure_annee_precedente' => 'decimal:2',
        'quantite_heure_annee_courante' => 'decimal:2'
    ];

    /**
     * Relation avec le code de travail
     */
    public function codeDeTravail()
    {
        return $this->belongsTo(CodeDeTravail::class);
    }

    /**
     * Relation avec l'année financière
     */
    public function anneeFinanciere()
    {
        return $this->belongsTo(AnneeFinanciere::class);
    }

    /**
     * Relation avec l'employé (à créer plus tard)
     */
    public function employe()
    {
        return $this->belongsTo('Modules\Rh\Models\Employe');
    }

    /**
     * Scope pour une année financière spécifique
     */
    public function scopeParAnneeFinanciere($query, $anneeFinanciereId)
    {
        return $query->where('annee_financiere_id', $anneeFinanciereId);
    }

    /**
     * Scope pour un employé spécifique
     */
    public function scopeParEmploye($query, $employeId)
    {
        return $query->where('employe_id', $employeId);
    }

    /**
     * Scope pour les configurations d'employés (exclut les jours fériés globaux)
     */
    public function scopeEmployeConfigurations($query)
    {
        return $query->whereNotNull('employe_id');
    }

    /**
     * Scope pour les configurations globales (jours fériés, etc.)
     */
    public function scopeGlobalConfigurations($query)
    {
        return $query->whereNull('employe_id');
    }

    /**
     * Scope pour les jours fériés
     */
    public function scopeJoursFeries($query)
    {
        return $query->whereHas('codeDeTravail', function ($q) {
            $q->where('code', 'FERIE');
        });
    }

    /**
     * Scope pour les congés
     */
    public function scopeConges($query)
    {
        return $query->whereHas('codeDeTravail', function ($q) {
            $q->where('categorie', CategorieCodeDeTravail::CONGE);
        });
    }

    /**
     * Calculer le solde restant
     */
    public function getSoldeRestantAttribute()
    {
        return $this->nombre_d_heure_restant + $this->solde_heure_annee_precedente;
    }

    /**
     * Calculer le pourcentage utilisé
     */
    public function getPourcentageUtiliseAttribute()
    {
        if ($this->nombre_d_heure <= 0) {
            return 0;
        }

        return round(($this->nombre_d_heure_pris / $this->nombre_d_heure) * 100, 2);
    }

    /**
     * Vérifier si des heures sont disponibles
     */
    public function hasHeuresDisponibles()
    {
        return $this->solde_restant > 0;
    }

    /**
     * Consommer des heures
     */
    public function consommerHeures($heures)
    {
        if ($heures > $this->solde_restant) {
            throw new \Exception('Heures demandées supérieures au solde disponible');
        }

        $this->nombre_d_heure_pris += $heures;
        $this->nombre_d_heure_restant -= $heures;
        $this->save();

        return $this;
    }

    /**
     * Rembourser des heures
     */
    public function rembourserHeures($heures)
    {
        $this->nombre_d_heure_pris = max(0, $this->nombre_d_heure_pris - $heures);
        $this->nombre_d_heure_restant = min(
            $this->nombre_d_heure + $this->solde_heure_annee_precedente,
            $this->nombre_d_heure_restant + $heures
        );
        $this->save();

        return $this;
    }

    /**
     * Copier la configuration vers une nouvelle année
     */
    public function copyToNewYear(AnneeFinanciere $nouvelleAnnee)
    {
        $heureDisponible = 0;
        if ($this->solde_heure_annee_precedente && $this->nombre_d_heure_restant) {
            $heureDisponible = $this->nombre_d_heure_restant + $this->solde_heure_annee_precedente;
        }

        return self::create([
            'libelle' => $this->libelle,
            'code_de_travail_id' => $this->code_de_travail_id,
            'annee_financiere_id' => $nouvelleAnnee->id,
            'nombre_d_heure' => $this->nombre_d_heure,
            'jour' => $this->jour,
            'nombre_d_heure_restant' => $heureDisponible,
            'nombre_d_heure_pris' => 0,
            'solde_heure_annee_precedente' => 0,
            'quantite_heure_annee_courante' => $heureDisponible,
            'employe_id' => $this->employe_id,
            'description' => $this->description,
            'debut' => $this->debut,
            'fin' => $this->fin
        ]);
    }

    /**
     * Obtenir les configurations pour un employé et une année
     */
    public static function getForEmployeAndAnnee($employeId, $anneeFinanciereId)
    {
        return self::parEmploye($employeId)
                   ->parAnneeFinanciere($anneeFinanciereId)
                   ->with('codeDeTravail')
                   ->get();
    }

    /**
     * Obtenir toutes les configurations d'une année (pour transfert)
     */
    public static function getForTransfer($anneeFinanciereId)
    {
        return self::parAnneeFinanciere($anneeFinanciereId)
                   ->employeConfigurations()
                   ->with(['codeDeTravail', 'employe'])
                   ->get();
    }

    public function __toString()
    {
        return $this->libelle ?: ($this->codeDeTravail ? $this->codeDeTravail->libelle : 'Configuration');
    }

    // protected static function newFactory(): ConfigurationCodeDeTravailFactory
    // {
    //     // return ConfigurationCodeDeTravailFactory::new();
    // }
}
