<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use App\Models\AdminBackend;
use Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile()
    {
        $titles = "PROFILE";
    	$profile = AdminBackend::where('id', Auth::guard('backend')->user()->id)->first();
    	
        return view('superadmin.profile.index', compact('profile', 'titles'));
    }

    public function save_profile(Request $request)
    {
    	try {

		  	$update = AdminBackend::where('id', Auth::guard('backend')->user()->id)->update([
	    		'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
	    		'email' => $request->input('email'),
	    		'phone' => $request->input('phone'),
	    	]);

		  	if ($update) {
		  		Alert::success('Success', 'Profile updated !');
		  	}else{
		  		Alert::error('Error', 'Failed');
		  	}

		} catch (\Exception $e) {
			Alert::error('Error', $e->getMessage());
		}

    	return back();
    }

    public function change_password(Request $request)
    {
    	try {

		  	$update = AdminBackend::where('id', Auth::user()->id)->update([
	    		'password' => Hash::make($request->input('password'))
	    	]);

		  	if ($update) {
		  		Alert::success('Success', 'Password changed');
		  	}else{
		  		Alert::error('Error', 'Gagal');
		  	}

		} catch (\Exception $e) {
			Alert::error('Error', $e->getMessage());
		}

    	return back();
    }
}
