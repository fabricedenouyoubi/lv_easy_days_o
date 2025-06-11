<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    protected $table = 'content_types';
    protected $fillable = ['app_label', 'model'];

        public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
