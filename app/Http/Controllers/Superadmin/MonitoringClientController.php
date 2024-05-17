<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Auth;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Discussion;
use App\Models\UploadFile;
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
        
        /* dashboard */
        $total_documents = 0;
        $total_users = 0;
        $total_qna = 0;
        $total_size = 0;
        if (!empty($clients->id)) {
            $total_documents = UploadFile::where('client_id', $clients->client_id)->count();
            $total_users = ClientUser::where('client_id', $clients->client_id)->count();
            $total_qna = Discussion::where('client_id', $clients->client_id)->count();
            $total_size = \globals::formatBytes(\DB::table('upload_files')->where('client_id', $clients->client_id)->sum('size'));
        }

        /* users */
        $clientuser = [];
        if (!empty($clients->id)) {
            $clientuser = ClientUser::orderBy('group_id', 'ASC')->where('client_id', $clients->client_id)->orderBy('id', 'DESC')->get();
        }

        return view('superadmin.monitoring_client.detail', compact('titles', 'clients', 'client', 'total_documents', 'total_users', 'total_qna', 'total_size', 'clientuser'));
    }
}
