<?php

namespace Modules\Entreprise\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Entreprise\Database\Factories\SiteFactory;

class Site extends Model
{
    use HasFactory;

    protected $table = 'sites';

    protected $fillable = [
        'name',
        'description',
        'entreprise_id',
        'adresse_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function adresse()
    {
        return $this->belongsTo(Adresse::class);
    }

    public function scopeForEntreprise($query, $entrepriseId)
    {
        return $query->where('entreprise_id', $entrepriseId);
    }

    public static function createWithAdresse(array $siteData, array $adresseData, $entrepriseId = null)
    {
        // Utiliser l'entreprise par défaut si non spécifiée
        if (!$entrepriseId) {
            $entreprise = Entreprise::getOrCreateDefault();
            $entrepriseId = $entreprise->id;
        }

        // Créer l'adresse
        $adresse = Adresse::create($adresseData);

        // Créer le site
        $site = self::create([
            'name' => $siteData['name'],
            'description' => $siteData['description'] ?? '',
            'entreprise_id' => $entrepriseId,
            'adresse_id' => $adresse->id
        ]);

        return $site;
    }
    public function updateWithAdresse(array $siteData, array $adresseData)
    {
        if ($this->adresse) {
            $this->adresse->update($adresseData);
        } else {
            // Créer une nouvelle adresse si elle n'existe pas
            $adresse = Adresse::create($adresseData);
            $this->adresse_id = $adresse->id;
        }

        $this->update([
            'name' => $siteData['name'],
            'description' => $siteData['description'] ?? $this->description
        ]);

        return $this;
    }

    // Boot method pour gerer la suppression en cascade
    protected static function boot()
    {
        parent::boot();

        // Supprimer l'adresse quand le site est supprimé
        static::deleting(function ($site) {
            if ($site->adresse) {
                $site->adresse->delete();
            }
        });
    }

    // protected static function newFactory(): SiteFactory
    // {
    //     // return SiteFactory::new();
    // }
}
