<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessGroup extends Model
{
    use HasFactory;
    
    protected $table = 'access_group';

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }

    public function RefClient()
    {
        return $this->hasOne('App\Models\Client', 'client_id' , 'client_id');
    }

    public function RefClientUser()
    {
        return $this->hasMany('App\Models\ClientUser', 'group_id' , 'group_id');
    }

    public function RefAssignUserGroup()
    {
        return $this->hasMany('App\Models\AssignUserGroup', 'group_id' , 'group_id');
    }
}
