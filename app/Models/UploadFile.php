<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'directory',
        'name',
        'basename',
        'description',
        'access_user',
        'mime_type',
        'size',
        'uploaded_by',
    ];
}
