<?php

namespace Modules\Rh\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Rh\Database\Factories\PosteFactory;

class Poste extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
        protected $fillable = [
        'libelle',
        'actif',
        'description',
    ];

    // protected static function newFactory(): PosteFactory
    // {
    //     // return PosteFactory::new();
    // }
}
