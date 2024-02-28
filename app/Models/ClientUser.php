<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_address',
        'company',
        'role',
        'role_param',
        'group_id',
        'status',
    ];
}
