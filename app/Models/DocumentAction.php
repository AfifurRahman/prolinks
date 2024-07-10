<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAction extends Model
{
    use HasFactory;

    protected $table = 'action_document';

    protected $fillable = [
        'project_id',
        'subproject_id',
        'user_id',
        'status',
        'action_type',
        'items_basename',
    ];
}
