<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AdminBackend extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'admin_backend';

    protected $guard = 'backend';

    protected $fillable = [
    	'email',
    	'password',
    ];

    public function RefRole()
    {
    	return $this->hasOne('App\Models\Role', 'id', 'role');
    }
}
