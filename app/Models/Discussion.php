<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    use HasFactory;

    protected $table = 'discussions';

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }

    public function RefClosedUser()
    {
        return $this->hasOne('App\Models\User', 'id' , 'closed_by');
    }

    public function RefProject()
    {
        return $this->hasOne('App\Models\Project', 'project_id' , 'project_id');
    }

    public function RefDiscussion()
    {
        return $this->hasMany('App\Models\DiscussionComment', 'discussion_id' , 'discussion_id')->where('deleted', 0);
    }
}
