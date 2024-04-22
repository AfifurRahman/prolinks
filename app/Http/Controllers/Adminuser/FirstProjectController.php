<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use App\Models\Project;
use Session;
use Auth;

class FirstProjectController extends Controller
{
    public function create_first_project()
    {
        $models = Project::where('user_id', Auth::user()->user_id)->get();
        if ($models->count() > 0) {
            return redirect(route('home'));
        }

        return view('adminuser.project.create_first_project');
    }

    public function save_first_project(Request $request)
    {
        $notification = "";
        try {
    		\DB::beginTransaction();
    		
            $project = new Project;
            $project->project_id = Str::uuid(4);
            $project->user_id = Auth::user()->user_id;
            $project->client_id = \globals::get_client_id();
            $project->company_id = "-";
            $project->project_name = $request->input('project_name');
            $project->project_desc = $request->input('project_desc');
            $project->start_date = $request->input('start_date');
            $project->deadline = $request->input('deadline');
            $project->created_by = Auth::user()->id;
            
            if ($project->save()) {
                $sub_project = new Project;
                $sub_project->project_id = Str::uuid(4);
                $sub_project->user_id = $project->user_id;
                $sub_project->company_id = "-";
                $sub_project->client_id = $project->client_id;
                $sub_project->project_name = "Default subproject";
                $sub_project->parent = $project->id;
                $sub_project->created_by = $project->created_by;
                $sub_project->created_at = $project->created_at;
                $sub_project->save();
                
                /* add session project id */
                Session::put('project_id', $project->project_id);
                User::where('id', Auth::user()->id)->update(['session_project'=> $project->project_id]);
                $notification = "Project created!";
            }

    		\DB::commit();
    	} catch (\Exception $e) {
    		\DB::rollback();
			Alert::error('Error', $e->getMessage());
    	    return back();
        }

    	return redirect(route('home'))->with('notification', $notification);
    }
}
