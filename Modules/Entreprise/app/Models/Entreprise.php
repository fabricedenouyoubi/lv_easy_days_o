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
        'description'
    ];

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
                'description' => 'La Table de concertation des organismes au service des personnes réfugiées et immigrantes (TCRI) est un regroupement de plus de 150 organismes œuvrant auprès des personnes réfugiées, immigrantes et sans statut'
            ]);
        }
        
        return $entreprise;
    }

    // protected static function newFactory(): EntrepriseFactory
    // {
    //     // return EntrepriseFactory::new();
    // }
}
