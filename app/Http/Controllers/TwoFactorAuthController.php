<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use Laravel\Fortify\RecoveryCode;
use Laravel\Fortify\Fortify;
use Illuminate\Validation\ValidationException;

class TwoFactorAuthController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Ensure two-factor secret is set
      
        $secret = $user->generateTwoFactorSecret();
        $recoveryCodes = json_encode($user->generateRecoveryCodes());

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt($recoveryCodes),
        ])->save();
       

        $svg = $this->generateQrCode($user->twoFactorQrCodeUrl());

        return view('adminuser.setting.two-factor-auth', ['svg' => $svg, 'recoveryCodes' => $recoveryCodes]);
    }   

    protected function generateQrCode($url)
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );

        $writer = new \BaconQrCode\Writer($renderer);
        return $writer->writeString($url);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Check if the user has two-factor authentication enabled
        if (! $user->two_factor_secret) {
            throw ValidationException::withMessages([
                'code' => ['Two-factor authentication is not enabled for this user.'],
            ]);
        }

        // Verify the two-factor authentication code
        $google2fa = new Google2FA();
        if (! $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code)) {
            throw ValidationException::withMessages([
                'code' => ['Invalid two-factor authentication code.'],
            ]);
        }

        if ($google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code)) {
            User::where('user_id', Auth::user()->user_id)->update(['two_factor_confirmed_at' => now()]);
        }

        // $user->setTwoFactorAuthenticated(true); // Implement this if you have such a method

        // Redirect the user to their intended destination
        return redirect()->intended('/setting?tab=account_setting');
    }

    public function login() {
        return view('auth.2fa_verify');
    }
}
