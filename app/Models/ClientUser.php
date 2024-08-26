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
        'name',
        'client_id',
        'company',
        'role',
        'role_param',
        'group_id',
        'status',
        'created_by'
    ];

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }

    public function RefClient()
    {
        return $this->hasOne('App\Models\Client', 'client_id' , 'client_id');
    }

    public function RefCreatedName()
    {
        return $this->hasOne('App\Models\User', 'id' , 'created_by');
    }

    public function assignProjects()
    {
        return $this->hasMany(AssignProject::class, 'user_id', 'user_id');
    }
}
