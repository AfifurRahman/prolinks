<?php

namespace App\Http\Controllers\Adminuser\Auth;

use App\Http\Controllers\Controller;
use App\Models\AssignProject;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClientUser;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Session;


class AuthController extends Controller
{
    public function create_password(Request $request, $token)
    {
        $check_set_pswd = $this->validate_check_set_pswd($token, $request->input('email'));
        if ($check_set_pswd) {
            return redirect(route('login'));
        }

    	$email = $request->input('email');
    	return view('adminuser.auth.create_password', compact('email', 'token'));
    }

    public function save_password(Request $request, $token, $email)
    {
        $this->validate($request, [
			'fullname' => 'required',
            'password' => 'required|same:confirm_password',
        ]);

    	try {
    		\DB::beginTransaction();

    		$check_users = User::where('email', $email)->where('remember_token', $token)->first();
    		$users_type = User::where('email', $email)->value('type');
			$project_id = AssignProject::where('user_id', $check_users->user_id)->orderBy('id', 'DESC')->value('project_id');

			if (!empty($check_users->email)) {
    			$update = User::where('email', $email)->where('remember_token', $token)->update([
    				'name' => $request->input('fullname'),
					'email_verified_at' => date('Y-m-d H:i:s'),
    				'password' => Hash::make($request->input('password')),
    			    'password_created' => \globals::create_pswd_client_yes(),
					'session_project' => $project_id
                ]);

    			if ($update) {
    				Session::flash('message', 'Password created !'); 
					if ($users_type == 0 || $users_type == 1 || $users_type == 2) {
						ClientUser::where('email_address',$email)->update([
							'status' => 1,
							'name' => $request->input('fullname')
						]);
					}
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

    public function validate_check_set_pswd($token, $email)
    {
        $get_users = User::where('remember_token', $token)->where('email', $email)->first();

        $result = false;
        if (!empty($get_users->email) && $get_users->password_created == \globals::create_pswd_client_yes()) {
            $result = true;
        }

        return $result;
    }
}
