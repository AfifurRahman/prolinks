<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project';

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }

    public function RefClient()
    {
        return $this->hasOne('App\Models\Client', 'client_id' , 'client_id');
    }

    public function RefSubProject(){
        return $this->hasMany('App\Models\SubProject', 'project_id' , 'project_id');
    }

    public function RefAssignProject(){
        return $this->hasMany('App\Models\AssignProject', 'project_id' , 'project_id');
    }
}
