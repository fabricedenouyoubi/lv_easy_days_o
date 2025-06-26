<?php

namespace Modules\RhFeuilleDeTempsAbsence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\RhFeuilleDeTempsAbsence\Database\Factories\OperationFactory;

class Operation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): OperationFactory
    // {
    //     // return OperationFactory::new();
    // }
}
