<?php

namespace Modules\Budget\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Modules\Budget\Database\Factories\AnneeFinanciereFactory;

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

    // Recuperer l'annee financiere active
    public static function getAnneeActive(){
        return self::actif()->first();
    }

    // Vérifier les chevauchements de dates (Expirer de l'ancien projet django)
    public static function hasDateOverlap($debut, $fin, $excludeId = null)
    {
        $query = self::where(function ($q) use ($debut, $fin) {
            $q->where('debut', '<=', $fin)
              ->where('fin', '>=', $debut);
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    // Valider la date de debut
    public static function isValideDateDebut($debut)
    {
        try {
            $date = Carbon::parse($debut);
            // Doit commencer le 1er avril
            return $date->month == 4 && $date->day == 1;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Valider la date de fin
    public static function isValideDateFin($debut, $fin)
    {
        try {
            $dateDebut = Carbon::parse($debut);
            $dateFin = Carbon::parse($fin);
            
            // Doit finir le 31 mars de l'année suivante
            return $dateFin->month == 3 && 
                   $dateFin->day == 31 && 
                   $dateFin->year == ($dateDebut->year + 1);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Methode pour cloturer une annee et creer la suivante
    public function cloturerEtCreerSuivante()
    {
        $anneeFin = $this->fin->year;
        $dateDebut = Carbon::create($anneeFin, 4, 1);
        $dateFin = Carbon::create($anneeFin + 1, 3, 31);

        // Désactiver toutes les autres années
        self::query()->update([
            'statut' => self::STATUT_INACTIF,
            'actif' => false
        ]);

        // Créer la nouvelle année
        return self::create([
            'debut' => $dateDebut,
            'fin' => $dateFin,
            'statut' => self::STATUT_ACTIF,
            'actif' => true
        ]);
    }
    // Activer cette annee - en cours  et desactiver les autres
    public function activer()
    {
        // Désactiver toutes les autres
        self::where('id', '!=', $this->id)->update([
            'statut' => self::STATUT_INACTIF,
            'actif' => false
        ]);

        // Activer celle-ci
        $this->update([
            'statut' => self::STATUT_ACTIF,
            'actif' => true
        ]);

        return $this;
    }
     
    // Accesseur pour formater le libelle d'une annee financiere
   public function getLibelleAttribute()
    {
        return $this->debut->format('Y') . ' - ' . $this->fin->format('Y');
    }

    // Accesseur pour formate le statut
    public function getStatutFormatteAttribute()
    {
        return self::getStatuts()[$this->statut] ?? $this->statut;
    }

    // Methode pour se rassurer qu'une seule annee est active
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->statut === self::STATUT_ACTIF && $model->actif) {
                // Désactiver toutes les autres années
                self::query()->update([
                    'statut' => self::STATUT_INACTIF,
                    'actif' => false
                ]);
            }
        });

        static::updating(function ($model) {
            if ($model->statut === self::STATUT_ACTIF && $model->actif) {
                // Désactiver toutes les autres années
                self::where('id', '!=', $model->id)->update([
                    'statut' => self::STATUT_INACTIF,
                    'actif' => false
                ]);
            }
        });
    }

    protected static function newFactory()
    {
        //return \Modules\Budget\Database\factories\AnneeFinanciereFactory::new();
    }
}
