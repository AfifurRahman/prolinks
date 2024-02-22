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
        \role::check_permission(array('list-monitoring'));

        $titles = "MONITORING CLIENT";
        $clients = Client::where('client_status', \globals::set_status_active())->get();
    	return view('superadmin.monitoring_client.index', compact('titles', 'clients'));
    }

    public function detail($id)
    {
        \role::check_permission(array('detail-monitoring'));

        $titles = "DETAIL MONITORING CLIENT";
        $clients = Client::where('client_status', \globals::set_status_active())->where('client_id', $id)->first();
    	$client = Client::where('client_status', \globals::set_status_active())->get();
        return view('superadmin.monitoring_client.detail', compact('titles', 'clients', 'client'));
    }
}
