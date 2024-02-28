<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use App\Models\Project;
use App\Models\ClientUserGroup;
use App\Models\ClientUser;
use Session;
use Auth;

class ProjectController extends Controller
{
	public function list_project()
	{
		$project = Project::where('user_id', Auth::user()->user_id)->get();
		$client_group = ClientUserGroup::get();

		return view('adminuser.project.list_project', compact('project', 'client_group'));
	}

	public function detail_project($id)
	{
		return view('adminuser.project.detail_project');
	}

	public function create_project($id=NULL)
	{
		$project = [];
		if ($id != NULL) {
			$project = Project::where('user_id', Auth::user()->user_id)->where('project_id', $id)->first();
		}

		return view('adminuser.project.add_project', compact('project'));
	}

	public function save_project(Request $request)
	{
		$projectId = $request->input('id');
		try {
			\DB::beginTransaction();

			if ($projectId != NULL) {
				$updated = Project::where('project_id', $projectId)->update([
					'role_group_id' => $request->input('role_group_id'),
					'project_name' => $request->input('project_name'),
		            'project_desc' => $request->input('project_desc'),
		            'start_date' => $request->input('start_date'),
		            'deadline' => $request->input('deadline'),
		            'updated_by' => Auth::user()->id,
		            'updated_at' => date('Y-m-d H:i:s')
				]);

				if ($updated) {
	                $notification = "Project updated!";
	            }
			}else{
				$project = new Project;
	            $project->project_id = Str::uuid(4);
	            $project->user_id = Auth::user()->user_id;
	            $project->role_group_id = $request->input('role_group_id');
	            $project->project_name = $request->input('project_name');
	            $project->project_desc = $request->input('project_desc');
	            $project->start_date = $request->input('start_date');
	            $project->deadline = $request->input('deadline');
	            $project->created_by = Auth::user()->id;
	            $project->created_at = date('Y-m-d H:i:s');

	            if ($project->save()) {
	            	$notification = "Project created!";
	            	$projectId = $project->project_id;
	            }
			}

			\DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();
			Alert::error('Error', $e->getMessage());
			return back();
		}

		return redirect(route('project.list-project'))->with('notification', $notification);
	}

	public function delete_project($id)
	{
		try {
			\DB::beginTransaction();

			$deleted = Project::where('project_id', $id)->delete();
			if ($deleted) {
				$notification = "Project deleted!";
			}

			\DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();
			Alert::error('Error', $e->getMessage());
			return back();
		}

		return back()->with('notification', $notification);
	}

	public function detail_role_users(Request $request)
	{
		$id = $request->input('id');

		$models = ClientUser::where('group_id', $id)->get();
		return response()->json($models);
	}

    public function change_main_project(Request $request)
    {
    	Session::put('project_id', $request->input('main_project_id'));
    	return back();
    }
}