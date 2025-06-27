<?php

namespace Modules\RhFeuilleDeTempsAbsence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Budget\Models\SemaineAnnee;
use Modules\Rh\Models\Employe\Employe;
use Modules\RhFeuilleDeTempsReguliere\Models\LigneTravail;

// use Modules\RhFeuilleDeTempsAbsence\Database\Factories\OperationFactory;

class Operation extends Model
{
    use HasFactory;

    protected $table = 'operations';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'workflow_log',
        'statut',
        // Nouvelle colonne pour le workflow
        'workflow_state',
        'total_heure',
        'total_heure_deplacement',
        'total_heure_regulier',
        'total_heure_supp',
        'total_heure_supp_ajuster',
        'total_heure_formation',
        'total_heure_sup_a_payer',
        'total_heure_csn',
        'total_heure_caisse',
        'total_heure_conge_mobile',
        'demande_absence_id',
        'employe_id',
        'annee_semaine_id',
    ];

    //--- relation avec demande d'absence
    public function demandeAbsence()
    {
        return $this->belongsTo(DemandeAbsence::class, 'demande_absence_id');
    }

    //--- relation avec employe
    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    //--- relation avec annee semaine
    public function anneeSemaine()
    {
        return $this->belongsTo(SemaineAnnee::class, 'annee_semaine_id');
    }

    /**
     * Relation avec les lignes de travail
     */
    public function lignesTravail()
    {
       return $this->hasMany(LigneTravail::class);
    }

    // Méthode du workflow
    /**
     * Obtenir l'état actuel du workflow
     */
    public function getCurrentState(): string
    {
        return $this->workflow_state ?? 'brouillon';
    }

    /**
     * Logger une transition de workflow
     */
    public function logTransition(string $from, string $to, ?string $comment = null): void
    {
        $log = [
            'timestamp' => now()->format('Y-m-d H:i'),
            'date' => now()->format('d-m-Y'),
            'time' => now()->format('H:i'),
            'from_state' => $from,
            'to_state' => $to,
            'comment' => $comment ?? '',
            'title' => "{$from} → {$to}",
            'user' => auth()->user()->name ?? 'System'
        ];

        $logs = $this->workflow_log ? explode("\n", $this->workflow_log) : [];
        $logs[] = json_encode($log);
        
        $this->update(['workflow_log' => implode("\n", $logs)]);
    }
    
    /**
     * Scope pour filtrer par employé
     */
    public function scopeParEmploye($query, $employeId)
    {
        return $query->where('employe_id', $employeId);
    }

    /**
     * Scope pour filtrer par semaine d'année
     */
    public function scopeParSemaine($query, $semaineId)
    {
        return $query->where('annee_semaine_id', $semaineId);
    }

    /**
     * Scope pour les opérations en attente (soumises)
     */
    public function scopeEnAttente($query)
    {
        return $query->where('workflow_state', 'soumis');
    }

    /**
     * Scope pour les opérations validées
     */
    public function scopeValidees($query)
    {
        return $query->where('workflow_state', 'valide');
    }

    /**
     * Scope pour les opérations en cours de rédaction
     */
    public function scopeBrouillons($query)
    {
        return $query->whereIn('workflow_state', ['brouillon', 'en_cours']);
    }

    
    /**
     * Obtenir une opération pour un employé et une semaine spécifique
     */
    public static function getOperationEmployeSemaine($employeId, $semaineId)
    {
        return static::where('employe_id', $employeId)
                    ->where('annee_semaine_id', $semaineId)
                    ->first();
    }

    /**
     * Créer ou obtenir une opération pour un employé et une semaine
     */
    public static function getOrCreateOperation($employeId, $semaineId)
    {
        return static::firstOrCreate(
            [
                'employe_id' => $employeId,
                'annee_semaine_id' => $semaineId
            ],
            [
                'workflow_state' => 'brouillon',
                'statut' => 'Brouillon',
                'total_heure' => 0,
                'total_heure_regulier' => 0,
                'total_heure_supp' => 0,
            ]
        );
    }

    /**
     * Calculer le total des heures à partir des lignes de travail
     */
    public function calculerTotalHeures(): float
    {
        $total = 0;
        foreach ($this->lignesTravail as $ligne) {
            for ($jour = 0; $jour <= 6; $jour++) {
                $dureeField = "duree_{$jour}";
                $total += $ligne->$dureeField ?? 0;
            }
        }
        return $total;
    }
    // protected static function newFactory(): OperationFactory
    // {
    //     // return OperationFactory::new();
    // }
}
