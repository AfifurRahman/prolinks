<?php

namespace App\Http\Controllers;

use App\Models\ClientUser;
use App\Models\Discussion;
use App\Models\SubProject;
use App\Models\UploadFile;
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
            $total_documents = UploadFile::where('client_id', \globals::get_client_id())->count();
            $total_users = ClientUser::where('client_id', \globals::get_client_id())->count();
            $total_qna = Discussion::where('client_id', \globals::get_client_id())->count();
            $total_size = \globals::formatBytes(\DB::table('upload_files')->where('client_id', \globals::get_client_id())->sum('size'));
            return view('adminuser.dashboard.index', compact('total_documents', 'total_users', 'total_qna', 'total_size'));
        }else{
            $subProject = SubProject::where('subproject_id', Auth::user()->session_project)->first();
            return redirect(route('adminuser.documents.list', base64_encode($subProject->project_id.'/'.$subProject->subproject_id)));
        }
        
    }
}
