<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use App\Models\SubProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\ClientUser;
use App\Models\Company;
use Session;
use Auth;

class ProjectController extends Controller
{
	public function list_project()
	{
		$project = Project::where('client_id', \globals::get_client_id())->where('parent', 0)->where('project_status', \globals::set_project_status_active())->orderBy('id', 'DESC')->get();
		$company = Company::where('company_status', \globals::set_status_company_active())->get();
		$parentProject = Project::where('client_id', \globals::get_client_id())->where('parent', 0)->where('project_status', \globals::set_project_status_active())->get();

		return view('adminuser.project.list_project', compact('project', 'company', 'parentProject'));
	}

	public function detail_project($id)
	{
		return view('adminuser.project.detail_project');
	}

	public function save_project(Request $request)
	{
		$projectId = $request->input('id');
		try {
			\DB::beginTransaction();

			if ($projectId != NULL) {
				$updated = Project::where('project_id', $projectId)->update([
					'client_id' => \globals::get_client_id(),
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
	            $project->company_id = "-";
	            $project->client_id = \globals::get_client_id();
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

	public function save_subproject(Request $request)
	{
		$id = $request->input('id');
		$project_id = $request->input('project_id');
		try {
			\DB::beginTransaction();

			if ($id != NULL) {
				$updated = SubProject::where('id', $id)->update([
					'subproject_name' => $request->input('project_name'),
		            'subproject_desc' => $request->input('project_desc'),
		            'updated_by' => Auth::user()->id,
		            'updated_at' => date('Y-m-d H:i:s')
				]);

				if ($updated) {
	                $notification = "Subroject updated!";
	            }
			}else{
				$project = new SubProject;
				$project->subproject_id = Str::uuid(4);
	            $project->project_id = $project_id;
	            $project->user_id = Auth::user()->user_id;
	            $project->client_id = \globals::get_client_id();
	            $project->subproject_name = $request->input('project_name');
	            $project->subproject_desc = $request->input('project_desc');
	            $project->created_by = Auth::user()->id;
	            $project->created_at = date('Y-m-d H:i:s');

	            if ($project->save()) {
	            	$notification = "Subroject created!";
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

	public function terminate_project(Request $request)
	{
		$project_id = $request->input('project_id');
		try {
			\DB::beginTransaction();
			
			$terminate = Project::where('project_id', $project_id)->update([
				'project_status' => \globals::set_project_status_terminate(),
				'terminate_reason' => $request->input('terminate_reason'),
			]);

			if ($terminate) {
				$notification = 'Project terminated';
			}

			\DB::commit();
		} catch (\Exception $th) {
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

	public function delete_sub_project($id)
	{
		try {
			\DB::beginTransaction();

			$deleted = SubProject::where('subproject_id', $id)->delete();
			if ($deleted) {
				$notification = "Subproject deleted!";
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
		$notification = "";
		$subProject = SubProject::where('subproject_id', $request->input('main_project_id'))->first();
		
		Session::put('project_id', $request->input('main_project_id'));
		$update = User::where('id', Auth::user()->id)->update(['session_project'=> $request->input('main_project_id')]);
		if ($update) {
			$notification = "Project changed";
		}

		$uriPrev = parse_url(url()->previous(), PHP_URL_PATH);
		$explodeUri = explode('/', $uriPrev);
		$uri = $explodeUri[1];
		
		if (!empty($uri) && $uri == "documents") {
			return redirect(route('adminuser.documents.list', base64_encode($subProject->project_id.'/'.$subProject->subproject_id)))->with('notification', $notification);
		}else{
			return back()->with('notification', $notification);
		}
    }
}
