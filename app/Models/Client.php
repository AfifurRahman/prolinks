<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    public function RefUser()
    {
        return $this->hasOne('App\Models\User', 'user_id' , 'user_id');
    }

    public function RefClientUser()
    {
        return $this->hasMany('App\Models\ClientUser', 'client_id' , 'client_id');
    }

    public function RefPricing()
    {
        return $this->hasOne('App\Models\Pricing', 'id' , 'pricing_id');
    }
}
