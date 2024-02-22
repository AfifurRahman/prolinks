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

class FirstProjectController extends Controller
{
    public function create_first_project()
    {
        return view('adminuser.project.create_first_project');
    }

    public function save_first_project(Request $request)
    {
        try {
    		\DB::beginTransaction();
    		
            $project = new Project;
            $project->project_id = Str::uuid(4);
            $project->user_id = Auth::user()->user_id;
            $project->project_name = $request->input('project_name');
            $project->project_desc = $request->input('project_desc');
            $project->start_date = $request->input('start_date');
            $project->deadline = $request->input('deadline');
            $project->created_by = Auth::user()->id;
            
            if ($project->save()) {
                /* add session project id */
                Session::put('project_id', $project->project_id);
                toast("Project created!", "success");
            }

    		\DB::commit();
    	} catch (\Exception $e) {
    		\DB::rollback();
			Alert::error('Error', $e->getMessage());
    	}

    	return redirect(route('home'));
    }
}
