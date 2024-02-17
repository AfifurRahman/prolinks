<?php

namespace App\Http\Controllers\Superadmin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::guard('backend')->check()) {
            return redirect(route('backend.dashboard'));
        }

        return view('auth.login_backend');
    }

    public function process_login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        $credentials = $request->only('email','password');

        if (Auth::guard('backend')->attempt($credentials)) {
            return redirect(route('backend.dashboard'));
        }

        return redirect(route('backend-login'))->withInput()->withErrors(['error' => 'These credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('backend')->check()) {
            Auth::guard('backend')->logout();
            return redirect(route('backend-login'));
        }
    }
}
