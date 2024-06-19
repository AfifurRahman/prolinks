<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogViewDocument extends Model
{
    use HasFactory;

    protected $table = 'log_view_document';

    public function RefClient()
    {
        return $this->hasOne('App\Models\Client', 'client_id' , 'client_id');
    }

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }

    public function RefClientUser()
    {
        return $this->hasOne('App\Models\ClientUser', 'id' , 'clientuser_id');
    }

    public function RefFile()
    {
        return $this->hasOne('App\Models\UploadFile', 'basename' , 'document_id');
    }
}
