<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_address',
        'client_id',
        'company',
        'role',
        'role_param',
        'group_id',
        'status',
    ];

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }
}
