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

    public function RefDiscussionAttachFile()
    {
        return $this->hasMany('App\Models\DiscussionAttachFile', 'comment_id' , 'id');
    }

    public function RefDiscussionLinkFile()
    {
        return $this->hasMany('App\Models\DiscussionLinkFile', 'comment_id' , 'id');
    }
}
