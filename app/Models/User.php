<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Str; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'last_signed',
        'two_factor_secret',
        'two_factor_confirmed_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function RefClient()
    {
        return $this->hasOne('App\Models\Client', 'client_id' , 'client_id');
    }

    public function RefClientUser()
    {
        return $this->hasMany('App\Models\ClientUser', 'user_id' , 'user_id');
    }

    public function generateTwoFactorSecret()
    {
        $google2fa = new Google2FA();
        return $google2fa->generateSecretKey();
    }

    public function twoFactorQrCodeUrl()
    {
        $google2fa = new Google2FA();
        $company = 'Prolinks'; // Change this to your company name
        $email = $this->email; // Or any unique identifier for the user
        $secret = decrypt($this->two_factor_secret);

        return $google2fa->getQRCodeUrl(
            $company,
            $email,
            $secret
        );
    }

    // Generate recovery codes
    public function generateRecoveryCodes()
    {
        return collect(range(1, 8))->map(function () {
            return Str::random(8); // Generate a random string for each recovery code
        })->all();
    }
}
