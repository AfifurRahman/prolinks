<?php

namespace App\Http\Controllers\Adminuser;

use Auth;
use App\Models\User;
use App\Models\ClientUser;
use App\Models\ClientUserGroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class AccessUsersController extends Controller
{
    public function index()
    {
        $clientuser = ClientUser::orderBy('group_id', 'ASC')->where('company', Auth::user()->name)->get();

        $groupid = ClientUserGroup::pluck('id')->toArray();

        $owners = User::where('type', 1)->where('name', Auth::user()->name)->get();
        
        array_unshift($groupid, 0);

        return view('adminuser.users.index', compact('clientuser','groupid','owners'));
    }

    public function create_user(Request $request)
    {
        try {
            $emailAddresses = explode(',', $request->email_address);

            foreach ($emailAddresses as $email) {
                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Check if the email already exists
                    $existingUser = ClientUser::where('email_address', $email)->first();
                    
                    if (!$existingUser) {
                        // If the email doesn't exist, create a new user
                        ClientUser::create([
                            'email_address' => $email,
                            'company' => Auth::user()->name,
                            'role' => $request->role,
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

    public function create_group(Request $request)
    {
        try {
            $users = explode(',', $request->users);

            $group = ClientUserGroup::create([
                'group_name' => $request->group_name,
                'group_description' => $request->group_description,
            ]);

            foreach($users as $user) {
                ClientUser::where('email_address', $user)->update([
                    'group_id' => $group->id,  
                ]);
            };

            $notification = "Group created";
        } catch (\Exception $e) {
            $notification = "Can't add group";
        }
        return back()->with('notification', $notification);
    }

    public function move_group(Request $request)
    {
        $users = $request->username;

        $group = $request->group_num;

        $result = DB::table('client_users')
                ->where('email_address', $users)
                ->update(['group_id' => $group]);

        return back();
    }

    public function resend_email($encodedEmail)
    {
        $email = base64_decode($encodedEmail);

        $users = User::where('email', $email)->firstOrFail();

        $token = Password::getRepository()->create($users);

        $details = [
            'client_name' => $email,
            'link' => URL::to('/create-password') . '/' . $token . '?email=' . str_replace("@", "%40", $email),
        ];

        $sendMail = \Mail::to($users->email)->send(new \App\Mail\CreateAdminClientPassword($details));

        $notification = "Email sent";

        return back()->with('notification', $notification);
    }
}
