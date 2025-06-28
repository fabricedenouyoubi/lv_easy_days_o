<?php

namespace Modules\RhFeuilleDeTempsReguliere\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;

class LigneTravail extends Model
{
    use HasFactory;

    protected $table = 'lignes_travail';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Lundi (0)
        'debut_0', 'fin_0', 'duree_0',
        // Mardi (1)
        'debut_1', 'fin_1', 'duree_1',
        // Mercredi (2)
        'debut_2', 'fin_2', 'duree_2',
        // Jeudi (3)
        'debut_3', 'fin_3', 'duree_3',
        // Vendredi (4)
        'debut_4', 'fin_4', 'duree_4',
        // Samedi (5)
        'debut_5', 'fin_5', 'duree_5',
        // Dimanche (6)
        'debut_6', 'fin_6', 'duree_6',
        
        // Relations
        'operation_id',
        'codes_travail_id',
        
        // Nouveaux champs pour auto-remplissage
        'auto_rempli',
        'type_auto_remplissage',
        'demande_absence_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'debut_0' => 'datetime:H:i',
        'fin_0' => 'datetime:H:i',
        'debut_1' => 'datetime:H:i',
        'fin_1' => 'datetime:H:i',
        'debut_2' => 'datetime:H:i',
        'fin_2' => 'datetime:H:i',
        'debut_3' => 'datetime:H:i',
        'fin_3' => 'datetime:H:i',
        'debut_4' => 'datetime:H:i',
        'fin_4' => 'datetime:H:i',
        'debut_5' => 'datetime:H:i',
        'fin_5' => 'datetime:H:i',
        'debut_6' => 'datetime:H:i',
        'fin_6' => 'datetime:H:i',
        
        'duree_0' => 'decimal:2',
        'duree_1' => 'decimal:2',
        'duree_2' => 'decimal:2',
        'duree_3' => 'decimal:2',
        'duree_4' => 'decimal:2',
        'duree_5' => 'decimal:2',
        'duree_6' => 'decimal:2',
    ];

    // ================================
    // RELATIONS
    // ================================
    
    /**
     * Relation avec l'opération
     */
    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    /**
     * Relation avec le code de travail
     */
    public function codeTravail()
    {
        return $this->belongsTo(CodeTravail::class, 'codes_travail_id');
    }

    /**
     * Relation avec la demande d'absence (si auto-rempli)
     */
    public function demandeAbsence()
    {
        return $this->belongsTo(DemandeAbsence::class, 'demande_absence_id');
    }

    // ================================
    // MÉTHODES UTILES
    // ================================
    
    /**
     * Obtenir les jours de la semaine avec leurs données
     */
    public function getJoursData(): array
    {
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $data = [];
        
        for ($i = 0; $i <= 6; $i++) {
            $data[$i] = [
                'nom' => $jours[$i],
                'debut' => $this->{"debut_{$i}"},
                'fin' => $this->{"fin_{$i}"},
                'duree' => $this->{"duree_{$i}"} ?? 0,
            ];
        }
        
        return $data;
    }

    /**
     * Calculer le total des heures pour cette ligne
     */
    public function getTotalHeures(): float
    {
        $total = 0;
        for ($i = 0; $i <= 6; $i++) {
            $total += $this->{"duree_{$i}"} ?? 0;
        }
        return $total;
    }

    /**
     * Définir les heures pour un jour spécifique
     */
    public function setJourHeures(int $jour, ?string $debut = null, ?string $fin = null, ?float $duree = null): void
    {
        if ($jour < 0 || $jour > 6) {
            throw new \InvalidArgumentException('Le jour doit être entre 0 (Lundi) et 6 (Dimanche)');
        }
        
        $this->{"debut_{$jour}"} = $debut;
        $this->{"fin_{$jour}"} = $fin;
        $this->{"duree_{$jour}"} = $duree;
    }

    /**
     * Obtenir les heures pour un jour spécifique
     */
    public function getJourHeures(int $jour): array
    {
        if ($jour < 0 || $jour > 6) {
            throw new \InvalidArgumentException('Le jour doit être entre 0 (Lundi) et 6 (Dimanche)');
        }
        
        return [
            'debut' => $this->{"debut_{$jour}"},
            'fin' => $this->{"fin_{$jour}"},
            'duree' => $this->{"duree_{$jour}"} ?? 0,
        ];
    }

    /**
     * Remplir une semaine d'absence (comme dans Django)
     */
    public function remplirSemaineAbsence(\DateTime $dateDebut, \DateTime $dateFin, int $heuresParJour = 8): void
    {
        $currentDate = clone $dateDebut;
        
        while ($currentDate <= $dateFin) {
            $jourSemaine = $currentDate->format('N') - 1; // 0=Lundi, 6=Dimanche
            
            if ($jourSemaine >= 0 && $jourSemaine <= 6) {
                $this->setJourHeures(
                    $jourSemaine,
                    '08:00',
                    sprintf('%02d:00', 8 + $heuresParJour),
                    min($heuresParJour, 7) // Max 7h par jour
                );
            }
            
            $currentDate->add(new \DateInterval('P1D'));
        }
    }
}