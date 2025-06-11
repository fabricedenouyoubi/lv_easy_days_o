<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupPermission extends Pivot
{
    protected $table = 'group_permission';

    protected $fillable = [
        'group_id',
        'permission_id',
    ];

    public $timestamps = true;

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
