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
                $desc = Auth::user()->name." has been created project ".$project->project_name;
                \log::create($request->all(), "success", $desc);
                $notification = "Project created!";
            }

    		\DB::commit();
    	} catch (\Exception $e) {
            \log::create($request->all(), "error", $e->getMessage());

    		\DB::rollback();
			Alert::error('Error', $e->getMessage());
    	    return back();
        }

    	return redirect(route('home'))->with('notification', $notification);
    }
}
