<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use App\Models\AssignProject;
use App\Models\SettingEmailNotification;
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
		if (Auth::user()->type != \globals::set_role_administrator()) {
            return abort(404);
        }
		
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
					$desc = Auth::user()->name." has been updated project ".$request->input('project_name');
					\log::create($request->all(), "success", $desc);
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
					$desc = Auth::user()->name." has been created project ".$project->project_name;
					\log::create($request->all(), "success", $desc);

	            	$notification = "Project created!";
	            	$projectId = $project->project_id;
	            }
			}

			\DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();

			\log::create($request->all(), "error", $e->getMessage());
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
					$desc = Auth::user()->name." has been updated sub project ".$request->input('project_name');
					\log::create($request->all(), "success", $desc);
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
					$assign = new AssignProject;
					$assign->client_id = \globals::get_client_id();
					$assign->project_id = $project->project_id;
					$assign->subproject_id = $project->subproject_id;
					$assign->user_id = $project->user_id;
					$assign->clientuser_id = ClientUser::where('user_id', $project->user_id)->where('client_id', \globals::get_client_id())->value('id');
					$assign->email = User::where('user_id', $project->user_id)->value('email');
					$assign->created_by = $project->created_by;
					if ($assign->save()) {
						$settings = new SettingEmailNotification;
						$settings->client_id = $assign->client_id;
						$settings->user_id = $assign->user_id;
						$settings->project_id = $assign->project_id;
						$settings->subproject_id = $assign->subproject_id;
						$settings->clientuser_id = $assign->clientuser_id;
						$settings->created_by = $assign->created_by;
						$settings->created_at = date('Y-m-d H:i:s');
						$settings->save();

						$desc = Auth::user()->name." has been created sub project ".$project->subproject_name;
						\log::create($request->all(), "success", $desc);

						$notification = "Subroject created!";
					}
	            }
			}
			\DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();

			\log::create($request->all(), "error", $e->getMessage());
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
				$projname = Project::where('project_id', $project_id)->value('project_name');
				$desc = Auth::user()->name." has been terminate project ".$projname." with reason ".$request->input('terminate_reason');
				\log::create($request->all(), "success", $desc);
				
				$notification = 'Project terminated';
			}

			\DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();

			\log::create($request->all(), "error", $e->getMessage());
			Alert::error('Error', $e->getMessage());
			return back();
		}

		return redirect(route('project.list-project'))->with('notification', $notification);
	}

	public function delete_project($id)
	{
		try {
			\DB::beginTransaction();

			$deleted = Project::where('project_id', $id)->where('client_id', \globals::get_client_id())->delete();
			if ($deleted) {
				$projname = Project::where('project_id', $id)->value('project_name');
				$desc = Auth::user()->name." has been deleted project ".$projname;
				\log::create(request()->all(), "success", $desc);

				$notification = "Project deleted!";
			}

			\DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();

			\log::create(request()->all(), "error", $e->getMessage());
			Alert::error('Error', $e->getMessage());
			return back();
		}

		return back()->with('notification', $notification);
	}

	public function delete_sub_project($id)
	{
		try {
			\DB::beginTransaction();

			$deleted = SubProject::where('subproject_id', $id)->where('client_id', \globals::get_client_id())->delete();
			if ($deleted) {
				AssignProject::where('subproject_id', $id)->where('client_id', \globals::get_client_id())->delete();
				$projname = SubProject::where('subproject_id', $id)->value('subproject_name');
				$desc = Auth::user()->name." has been deleted sub project ".$projname;
				\log::create(request()->all(), "success", $desc);

				$notification = "Subproject deleted!";
			}

			\DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();

			\log::create(request()->all(), "error", $e->getMessage());
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
		try {
			$notification = "";
			$subProject = SubProject::where('subproject_id', $request->input('main_project_id'))->first();
			
			Session::put('project_id', $request->input('main_project_id'));
			
			$types = AssignProject::join('client_users', 'client_users.id', 'assign_project.clientuser_id')->where('assign_project.subproject_id', $request->input('main_project_id'))->where('assign_project.user_id', Auth::user()->user_id)->value('role');
			$client_id = AssignProject::join('client_users', 'client_users.id', 'assign_project.clientuser_id')->where('assign_project.subproject_id', $request->input('main_project_id'))->where('assign_project.user_id', Auth::user()->user_id)->value('client_users.client_id');
			
			$update = User::where('id', Auth::user()->id)->update([
				'type' => $types,
				'client_id' => $client_id,
				'session_project'=> $request->input('main_project_id')
			]);
			
			if ($update) {
				$desc = Auth::user()->name." switch to sub project ".$subProject->subproject_name;
				\log::create(request()->all(), "success", $desc);
				
				$notification = "Project changed";
			}

			$uriPrev = parse_url(url()->previous(), PHP_URL_PATH);
			$explodeUri = explode('/', $uriPrev);
			$uri = $explodeUri[1];
			
			if (!empty($uri) && $uri == "documents") {
				return redirect(route('adminuser.documents.list', base64_encode($subProject->project_id.'/'.$subProject->subproject_id)))->with('notification', $notification);
			}else if(!empty($uri) && $uri == "discussion") {
				return redirect(route('adminuser.documents.list', base64_encode($subProject->project_id.'/'.$subProject->subproject_id)))->with('notification', $notification);
			}
			else{
				return back()->with('notification', $notification);
			}

		} catch (\Exception $e) {

			\log::create($request->all(), "error", $e->getMessage());
			return back()->with('notification', "failed to change sub project");
		}
		
    }
}
