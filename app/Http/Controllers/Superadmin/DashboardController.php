<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
    	$titles = "DASHBOARD";
    	return view('superadmin.dashboard.index', compact('titles'));
    }

    public function not_found()
    {
    	$titles = "ACCESS DENIED";
        return view('superadmin.not_found', compact('titles'));
    }
}
