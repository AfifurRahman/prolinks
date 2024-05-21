<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AssignProject;
use App\Models\Project;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Auth;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Discussion;
use App\Models\UploadFile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MonitoringClientController extends Controller
{
    public function list()
    {
        \role::check_permission(array('list-monitoring'));

        $titles = "MONITORING CLIENT";
        $clients = ClientUser::orderBy('id', 'DESC')->get();
    	return view('superadmin.monitoring_client.index', compact('titles', 'clients'));
    }

    public function detail($id)
    {
        \role::check_permission(array('detail-monitoring'));

        $titles = "DETAIL MONITORING CLIENT";

        $id = base64_decode($id);
        $user = ClientUser::where('id', $id)->first();
    	$users = Client::get();

        if (!empty($user->RefUser->user_id)) {
            $user = User::where('user_id', $user->RefUser->user_id)->first();
            Auth::login($user);

            $name = Auth::guard('backend')->user()->first_name." ".Auth::guard('backend')->user()->last_name;
            $desc = $name." access dashboard user ".$user->name;
            \log::create(Auth::guard('backend')->user(), "success", $desc);

            return redirect(route('home'));
        }
    }
}
