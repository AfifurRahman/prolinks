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
        'directory',
        'name',
        'basename',
        'description',
        'access_user',
        'status',
        'uploaded_by',
    ];
}
