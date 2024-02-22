<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Project;
use Session;
use Auth;

class ProjectController extends Controller
{
    public function change_main_project(Request $request)
    {
    	Session::put('project_id', $request->input('main_project_id'));
    	return back();
    }
}
