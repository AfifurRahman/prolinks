<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'index',
        'project_id',
        'subproject_id',
        'directory',
        'name',
        'basename',
        'description',
        'client_id',
        'mime_type',
        'size',
        'status',
        'uploaded_by',
    ];
}
