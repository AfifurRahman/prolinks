<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Auth;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MonitoringClientController extends Controller
{
    public function list()
    {
        $titles = "MONITORING CLIENT";

        /* set permission */
        $permission = \role::get_permission(array('list-monitoring'));
        if ($permission == false) {
            return redirect(route('backend.not-found'));
        }

        $clients = Client::where('client_status', \globals::set_status_active())->get();
    	return view('superadmin.monitoring_client.index', compact('titles', 'clients'));
    }

    public function detail($id)
    {
        $titles = "DETAIL MONITORING CLIENT";

        /* set permission */
        $permission = \role::get_permission(array('detail-monitoring'));
        if ($permission == false) {
            return redirect(route('backend.not-found'));
        }

        $clients = Client::where('client_status', \globals::set_status_active())->where('client_id', $id)->first();
    	return view('superadmin.monitoring_client.detail', compact('titles', 'clients'));
    }
}
