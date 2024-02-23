<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientUserGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'group_description',
    ];
}
