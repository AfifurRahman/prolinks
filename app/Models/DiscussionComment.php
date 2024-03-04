<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionComment extends Model
{
    use HasFactory;

    protected $table = 'discussion_comments';

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }

    public function RefDiscussion()
    {
        return $this->hasMany('App\Models\Discussion', 'discussion_id' , 'discussion_id');
    }
}
