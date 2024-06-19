<?php

namespace App\Http\Controllers;

use App\Models\ClientUser;
use App\Models\Discussion;
use App\Models\LogActivity;
use App\Models\SubProject;
use App\Models\UploadFile;
use Illuminate\Http\Request;
use Auth;
use DB;
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
            $most_active_user = DB::select("SELECT b.name, b.avatar_color, b.email, COUNT(a.user_id) as total FROM log_activity a JOIN users b ON a.user_id = b.user_id where a.client_id = '".\globals::get_client_id()."' AND a.description LIKE '%logged in%' GROUP BY a.user_id, b.name, b.avatar_color, b.email ORDER BY total DESC LIMIT 4");
            $most_viewed_doc = DB::select("SELECT document_name, COUNT(user_id) as total FROM log_view_document where client_id = '".\globals::get_client_id()."' GROUP BY user_id, document_name ORDER BY total DESC LIMIT 4");
            
            return view('adminuser.dashboard.index', compact('total_documents', 'total_users', 'total_qna', 'total_size', 'most_active_user', 'most_viewed_doc'));
        }else{
            $subProject = SubProject::where('subproject_id', Auth::user()->session_project)->first();
            return redirect(route('adminuser.documents.list', base64_encode($subProject->project_id.'/'.$subProject->subproject_id)));
        }
        
    }
}
