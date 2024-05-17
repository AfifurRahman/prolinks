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
use Auth;


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
			$project_id = AssignProject::where('user_id', $check_users->user_id)->orderBy('id', 'DESC')->value('subproject_id');

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

					$desc = $request->input('fullname'). " success created password";
					\log::create($request->all(), "success", $desc);
    			}
    		}else{
				\log::create($request->all(), "error", 'Failed, client not found');
    			Session::flash('message', 'Failed, client not found'); 
				return back();
    		}

    		\DB::commit();
    	} catch (\Exception $e) {
    		\DB::rollback();

			\log::create($request->all(), "error", $e->getMessage());
    		Session::flash('message', $e->getMessage()); 
			return back();
    	}

    	return redirect(route('login'));
    }

    public function validate_check_set_pswd($token, $email)
    {
        $get_users = User::where('email', $email)->first();
		
        $result = false;
        if (!empty($get_users->email) && $get_users->password_created == \globals::create_pswd_client_yes()) {
            $this->update_account_exist($email);
			$result = true;
        }

        return $result;
    }

	public function update_account_exist($email)
    {
		/* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
		$client_users = ClientUser::where('email_address', $email)->where('status', 0)->first();
		if (!empty($client_users->id)) {
			ClientUser::where('id', $client_users->id)->update([
				'status' => 1
			]);
		}
	}
}
