<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{
    protected $table = 'group_user';

    protected $fillable = [
        'user_id',
        'group_id',
    ];

    public $timestamps = true;

    public function groups()
    {
        return $this->belongsToMany(Group::class)
            ->using(self::class)
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(self::class)
            ->withTimestamps();
    }
}
