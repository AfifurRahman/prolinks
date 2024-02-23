<?php

namespace App\Http\Controllers\Adminuser;

use App\Models\ClientUser;
use App\Models\ClientUserGroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AccessUsersController extends Controller
{
    public function index()
    {
        $clientuser = ClientUser::orderBy('group_id', 'ASC')->get();

        $groupnames =  ClientUserGroup::pluck('group_name')->toArray();

        $groupid = ClientUserGroup::pluck('id')->toArray();

        array_unshift($groupid, 0);

        return view('adminuser.users.index', compact('clientuser','groupid', 'groupnames'));
    }

    public function create_user(Request $request)
    {
        ClientUser::create([
            'email_address' => $request->email_address,
            'role' => $request->role,
            'role_param' => $request->role,
            'group_id' => $request->role,
            'status' => $request->role,
        ]);

        return back();
    }

    public function create_group(Request $request)
    {
        ClientUserGroup::create([
            'group_name' => $request->group_name,
            'group_description' => $request->group_description,
        ]);

        return back();
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
}
