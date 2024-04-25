<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignProject extends Model
{
    use HasFactory;

    protected $table = 'assign_project';

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }

    public function RefProject()
    {
        return $this->hasOne('App\Models\Project', 'project_id' , 'project_id');
    }
    
    // public function RefSubProject($parent){
    //     return Project::where('client_id', \globals::get_client_id())->where('parent', $parent)->where('project_status', \globals::set_project_status_active())->get();
    // }

    public function RefSubProject(){
        return $this->hasOne('App\Models\SubProject', 'subproject_id' , 'subproject_id');
    }
}
