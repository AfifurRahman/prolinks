<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watermark extends Model
{
    use HasFactory;

    protected $table = 'watermark';

    protected $fillable = [
        'client_id',
        'display_view',
        'display_printing',
        'display_download',
        'details_projectname',
        'details_fullname',
        'details_email',
        'details_companyname',
        'details_timestamp',
        'color',
        'opacity',
        'position',
    ];
}
