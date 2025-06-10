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

    protected $fillable = [
        'debut',
        'fin',
        'actif'
    ];

    protected $casts = [
        'debut' => 'date',
        'fin' => 'date',
        'actif' => 'boolean'
    ];

    // Scope pour l'année active
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    // Scope pour les années inactives
    public function scopeInactif($query)
    {
        return $query->where('actif', false);
    }

    // Récupérer l'année financière active
    public static function getAnneeActive()
    {
        return self::actif()->first();
    }

    // Vérifier les chevauchements de dates
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

    // Valider la date de début (1er avril)
    public static function isValideDateDebut($debut)
    {
        try {
            $date = Carbon::parse($debut);
            return $date->month == 4 && $date->day == 1;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Valider la date de fin (31 mars année suivante)
    public static function isValideDateFin($debut, $fin)
    {
        try {
            $dateDebut = Carbon::parse($debut);
            $dateFin = Carbon::parse($fin);
            
            return $dateFin->month == 3 && 
                   $dateFin->day == 31 && 
                   $dateFin->year == ($dateDebut->year + 1);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Méthode pour clôturer une année et créer la suivante
    public function cloturerEtCreerSuivante()
    {
        // Calculer les dates de la nouvelle année
        $anneeFin = $this->fin->year;
        $dateDebut = Carbon::create($anneeFin, 4, 1);
        $dateFin = Carbon::create($anneeFin + 1, 3, 31);

        // 1. Désactiver toutes les feuilles de temps
        if (class_exists('Modules\RhFeuilleDeTempsConfig\Models\FeuilleDeTemps')) {
            //\Modules\RhFeuilleDeTempsConfig\Models\FeuilleDeTemps::query()->update(['actif' => false]);
        }

        // 2. Désactiver toutes les autres années
        self::query()->update(['actif' => false]);

        // 3. Créer la nouvelle année
        $nouvelleAnnee = self::create([
            'debut' => $dateDebut,
            'fin' => $dateFin,
            'actif' => true
        ]);

        // 4. Déclencher la génération
        $this->triggerAnneeFinanciereSignals($nouvelleAnnee);

        return $nouvelleAnnee;
    }

    // Déclencher les "signaux" de la nouvelle année
    private function triggerAnneeFinanciereSignals($nouvelleAnnee)
    {
        // TODO: À implémenter lors de la création du module rh_feuille_de_temps_config
        // - generateJourFerie($nouvelleAnnee)
        // - generateFeuillesDeTemps($nouvelleAnnee)  
        // - transfererCodeTravailVersNouvelleAnnee($nouvelleAnnee, $this)
        // - updateAnneeFinanciereSessionData($nouvelleAnnee)
    }

    // Activer cette année et désactiver les autres
    public function activer()
    {
        // Désactiver toutes les autres
        self::where('id', '!=', $this->id)->update(['actif' => false]);

        // Activer celle-ci
        $this->update(['actif' => true]);

        return $this;
    }
     
    // Accesseur pour le libellé
    public function getLibelleAttribute()
    {
        return $this->debut->format('Y') . ' - ' . $this->fin->format('Y');
    }

    // Méthode pour s'assurer qu'une seule année est active
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->actif) {
                self::query()->update(['actif' => false]);
            }
        });

        static::updating(function ($model) {
            if ($model->actif) {
                self::where('id', '!=', $model->id)->update(['actif' => false]);
            }
        });
    }

    // Relation avec les feuilles de temps
    public function feuillesDeTemps()
    {
        return $this->hasMany('Modules\RhFeuilleDeTempsConfig\Models\FeuilleDeTemps');
    }

    // Relation avec les configurations de codes de travail
    public function configurationsCodeDeTravail()
    {
        return $this->hasMany('Modules\RhFeuilleDeTempsConfig\Models\ConfigurationCodeDeTravail');
    }

    // Méthode pour initialiser une nouvelle année avec tous les services
    public function initialiserAnnee()
    {
        if (class_exists('Modules\RhFeuilleDeTempsConfig\Services\AnneeFinanciereService')) {
            $service = app('Modules\RhFeuilleDeTempsConfig\Services\AnneeFinanciereService');
            return $service->initialiserNouvelleAnnee($this);
        }

        return $this;
    }

    // Obtenir les statistiques de l'année
    public function getStatistiques()
    {
        if (class_exists('Modules\RhFeuilleDeTempsConfig\Services\AnneeFinanciereService')) {
            $service = app('Modules\RhFeuilleDeTempsConfig\Services\AnneeFinanciereService');
            return $service->getAnneeFinanciereStats($this);
        }

        return [
            'total_feuilles' => 0,
            'feuilles_actives' => 0,
            'semaines_de_paie' => 0,
            'total_jours_feries' => 0,
            'total_configurations' => 0
        ];
    }
    protected static function newFactory()
    {
        //return \Modules\Budget\Database\factories\AnneeFinanciereFactory::new();
    }
}
