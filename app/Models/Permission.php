<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'content_type_id', 'codename'];
    protected $table = 'permissions';


    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'permission_user');
    }
}
