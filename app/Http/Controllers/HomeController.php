<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Project;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::user()->type == \globals::set_role_administrator()) {
            return view('adminuser.dashboard.index');
        }else{
            $getParent = Project::where('project_id', \globals::get_project_id())->value('parent');
            $projectId = Project::where('id', $getParent)->value('project_id');
            $subProject = \globals::get_project_id();
            return redirect(route('adminuser.documents.list', base64_encode($projectId.'/'.$subProject)));
        }
        
    }
}
