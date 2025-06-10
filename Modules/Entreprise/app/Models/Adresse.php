<?php

namespace Modules\Entreprise\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Entreprise\Database\Factories\AdresseFactory;

class Adresse extends Model
{
    use HasFactory;

    protected $table = 'adresses';

    protected $fillable = [
        'ville',
        'rue',
        'appartement',
        'code_postal',
        'telephone',
        'telephone_pro',
        'telephone_pro_ext'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function site()
    {
        return $this->hasOne(Site::class);
    }

    // Accesseur pour l'adresse complète formatée
    public function getAdresseCompleteAttribute()
    {
        $parts = array_filter([
            $this->appartement,
            $this->rue,
            $this->ville,
            $this->code_postal
        ]);
        
        return implode(' ', $parts);
    }

    public static function validateTelephone($value)
    {
        return preg_match('/^[0-9+]+$/', $value);
    }
    // protected static function newFactory(): AdresseFactory
    // {
    //     // return AdresseFactory::new();
    // }
}
