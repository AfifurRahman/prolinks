<?php

namespace App\Http\Controllers\Adminuser;

use Auth;
use App\Models\User;
use App\Models\ClientUser;
use App\Models\Company;
use App\Models\AccessGroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Session;

class AccessUsersController extends Controller
{
    public function index()
    {
        $adminusercompany = DB::table('clients')->where('client_email',Auth::user()->email)->value('client_id');

        $clientuser = ClientUser::orderBy('group_id', 'ASC')->where('company', $adminusercompany)->get();

        $group = AccessGroup::pluck('group_id')->toArray();

        $owners = User::where('type', 1)->where('name', Auth::user()->name)->get();
        
        array_unshift($group, 0);

        return view('adminuser.users.index', compact('clientuser','group','owners'));
    }

    public function create_user(Request $request)
    {
        try {
            $emailAddresses = explode(',', $request->email_address);

            foreach ($emailAddresses as $email) {
                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $existingUser = ClientUser::where('email_address', $email)->first();
                    
                    if (!$existingUser) {
                        ClientUser::create([
                            'email_address' => $email,
                            'company' => DB::table('clients')->where('client_email',Auth::user()->email)->value('client_id'),
                            'role' => $request->role,
                            'group_id' => $request->company,
                        ]);
        
                        $users = new User;
                        $users->user_id = Str::uuid(4);
                        $users->name = Auth::user()->name;
                        $users->email = $email;
                        $users->type = \globals::set_usertype_client();
                        $users->password = Hash::make(bcrypt(Str::random(255)));
                        $users->save();
        
                        $token = Password::getRepository()->create($users);
                        $details = [
                            'client_name' => $email,
                            'link' => URL::to('/create-password') . '/' . $token . '?email=' . str_replace("@", "%40", $email),
                        ];
        
                        $sendMail = \Mail::to($users->email)->send(new \App\Mail\CreateAdminClientPassword($details));

                        if ($sendMail) {
                          User::where('id', $users->id)->update([
                              'remember_token' => $token
                          ]);
                        }

                        $notification = "User invited";
                    } else {
                        $notification = "User already exist!";
                    };
                } else {
                    $notification = "Invalid email";
                }
            };
        } catch (\Exception $e) {
            $notification = "Can't invite user";
        }
        return back()->with('notification', $notification);   
    }

    public function move_group(Request $request)
    {
        try {
            $users = base64_decode($request->username);

            $group = $request->group_num;

            DB::table('client_users')->where('email_address', $users)->update(['group_id' => $group]);

            $notification = "User moved successfully";
        } catch (\Exception $e) {
            $notification = "Failed to move user";
        }

        return back()->with('notification', $notification);
    }

    public function resend_email($encodedEmail)
    {
        try {
            $email = base64_decode($encodedEmail);

            $users = User::where('email', $email)->firstOrFail();

            $token = Password::getRepository()->create($users);

            $details = [
                'client_name' => $email,
                'link' => URL::to('/create-password') . '/' . $token . '?email=' . str_replace("@", "%40", $email),
            ];

            \Mail::to($users->email)->send(new \App\Mail\CreateAdminClientPassword($details));

            $notification = "Email sent";
        } catch(\Exception $e) {
            $notification = "Failed to send email";
        }

        return back()->with('notification', $notification);
    }

    public function disable_user($encodedEmail)
    {
        try {
            $email = base64_decode($encodedEmail);

            ClientUser::where('email_address',$email)->update(['status' => 2]);
    
            $notification = "User has been disabled";    
        } catch(\Exception $e) {
            $notification = "Failed to disable user";
        }

        return back()->with('notification', $notification);
    }
    public function enable_user($encodedEmail)
    {
        try {
            $email = base64_decode($encodedEmail);

            ClientUser::where('email_address',$email)->update(['status' => 1]);
            
            $notification = "User has been enabled";
        } catch(\Exception $e) {
            $notification= "Failed to enable user";
        }
       
        return back()->with('notification', $notification);
    }

    public function create_group(Request $request) {
        try {
            $group = new AccessGroup;
            $group->group_id = Str::uuid(4);
            $group->project_id = Session::get('project_id');
            $group->client_id = \globals::get_client_id();
            $group->user_id = Auth::user()->user_id;
            $group->group_name = $request->input('group_name');
            $group->group_desc = $request->input('group_desc');
            $group->created_by = Auth::user()->id;
            $group->created_at = date('Y-m-d H:i:s');
            if($group->save()) {
                $notification = 'success create group';
            }
        } catch (\Throwable $th) {
            $notification = $th->getMessage();
        }

        return back()->with('notification', $notification);
    }
}