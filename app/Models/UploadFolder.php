<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'index',
        'project_id',
        'subproject_id',
        'parent',
        'directory',
        'name',
        'basename',
        'displayname',
        'description',
        'client_id',
        'status',
        'uploaded_by',
    ];
}
