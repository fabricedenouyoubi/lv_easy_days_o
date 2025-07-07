<?php

namespace Modules\Entreprise\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Entreprise\Database\Factories\EntrepriseFactory;

class Entreprise extends Model
{
    use HasFactory;

    protected $table = 'entreprises';

    protected $fillable = [
        'name',
        'description',
        'premier_jour_semaine'
    ];

    // Constantes pour les jours de la semaine
    const JOURS_SEMAINE = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche'
    ];

    // Accesseur pour le libellé du premier jour
    public function getPremierJourSemaineLibelleAttribute()
    {
        return self::JOURS_SEMAINE[$this->premier_jour_semaine] ?? 'Lundi';
    }

    // Méthode statique pour obtenir les options de jours
    public static function getJoursSemaineOptions()
    {
        return self::JOURS_SEMAINE;
    }
    /**
     * Obtenir le premier jour de la semaine de l'entreprise par défaut
     */
    public static function getPremierJourSemaine()
    {
        $entreprise = self::first();
        return $entreprise ? $entreprise->premier_jour_semaine : 1; // Lundi par défaut
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations
    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public static function getEntreprise()
    {
        return self::first();
    }

    public static function getOrCreateDefault()
    {
        $entreprise = self::first();

        if (!$entreprise) {
            $entreprise = self::create([
                'name' => 'TCRI Canada',
                'description' => 'La Table de concertation des organismes au service des personnes réfugiées et immigrantes (TCRI) est un regroupement de plus de 150 organismes œuvrant auprès des personnes réfugiées, immigrantes et sans statut',
                'premier_jour_semaine' => 1
            ]);
        }

        return $entreprise;
    }

    // protected static function newFactory(): EntrepriseFactory
    // {
    //     // return EntrepriseFactory::new();
    // }
}
