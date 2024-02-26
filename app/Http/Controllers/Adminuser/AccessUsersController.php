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
        $clientuser = ClientUser::orderBy('group_id', 'ASC')->get();

        $groupid = ClientUserGroup::pluck('id')->toArray();
        
        array_unshift($groupid, 0);

        return view('adminuser.users.index', compact('clientuser','groupid'));
    }

    public function create_user(Request $request)
    {
        $emailAddresses = explode(',', $request->email_address);

        foreach ($emailAddresses as $email) {
            ClientUser::create([
                'email_address' => $email,
                'role' => $request->role,
            ]);
        };

        $users = new User;
        $users->user_id = Str::uuid(4);
        $users->name = $request->email_address;
        $users->email = $request->email_address;
        $users->type = \globals::set_usertype_client();
        $users->password = Hash::make(bcrypt(Str::random(255)));
        $users->save();

        $token = Password::getRepository()->create($users);
        $details = [
            'client_name' => $request->email_address,
            'link' => URL::to('/create-password') . '/' . $token . '?email=' . str_replace("@", "%40", $request->email_address),
        ];

        $sendMail = \Mail::to($users->email)->send(new \App\Mail\CreateAdminClientPassword($details));

        $notification = "Users invited";

        return back()->with('notification', $notification);
    }

    public function create_group(Request $request)
    {
        ClientUserGroup::create([
            'group_name' => $request->group_name,
            'group_description' => $request->group_description,
        ]);

        $notification = "Group created";

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
