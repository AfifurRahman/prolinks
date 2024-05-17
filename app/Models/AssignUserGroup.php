<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignUserGroup extends Model
{
    use HasFactory;

    protected $table = 'assign_user_group';

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }

    public function RefClientUser()
    {
        return $this->hasOne('App\Models\ClientUser', 'user_id' , 'user_id')->where('client_id', \globals::get_client_id());
    }

    public function RefGroup()
    {
        return $this->hasOne('App\Models\AccessGroup', 'group_id' , 'group_id')->where('client_id', \globals::get_client_id());
    }
}
