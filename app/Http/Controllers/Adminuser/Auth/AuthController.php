<?php

namespace App\Http\Controllers\Adminuser\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Session;

class AuthController extends Controller
{
    public function create_password(Request $request, $token)
    {
    	$email = $request->input('email');
    	return view('adminuser.auth.create_password', compact('email', 'token'));
    }

    public function save_password(Request $request, $token, $email)
    {
    	try {
    		\DB::beginTransaction();

    		$check_users = User::where('email', $email)->where('remember_token', $token)->first();
    		if (!empty($check_users->email)) {
    			$update = User::where('email', $email)->where('remember_token', $token)->update([
    				'email_verified_at' => date('Y-m-d H:i:s'),
    				'password' => Hash::make($request->input('password')),
    			]);

    			if ($update) {
    				Session::flash('message', 'Password created !'); 
    			}
    		}else{
    			Session::flash('message', 'Failed, client not found'); 
				return back();
    		}

    		\DB::commit();
    	} catch (\Exception $e) {
    		\DB::rollback();
    		Session::flash('message', $e->getMessage()); 
			return back();
    	}

    	return redirect(route('login'));
    }
}
