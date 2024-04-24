<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryImportDiscussion extends Model
{
    use HasFactory;

    protected $table = 'history_import_discussions';

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }

    public function RefClient()
    {
        return $this->hasOne('App\Models\Client', 'client_id' , 'client_id');
    }

    public function RefProject()
    {
        return $this->hasOne('App\Models\Project', 'project_id' , 'project_id');
    }
}
