<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(Request $request){
        \role::check_permission(array('log-activity'));

    	$titles = "LOG ACTIVITY";
    	$log = \log::get_all($request);
        $client = Client::get();
    	
        return view('superadmin.log.index', compact('log', 'client', 'titles'));
    }
}