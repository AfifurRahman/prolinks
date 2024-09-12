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
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Project;
use App\Models\UploadFile;
use App\Models\UploadFolder;
use App\Models\Company;
use Carbon\Carbon;
use Session;
use Auth;
use ZipArchive;

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

	public function recycle_bin() 
	{
		if (Auth::user()->type != \globals::set_role_administrator()) {
            return abort(404);
        }

		$project = Project::where('client_id', \globals::get_client_id())->where('parent', 0)->where('project_status', \globals::set_project_status_active())->orderBy('id', 'DESC')->get();
		$company = Company::where('company_status', \globals::set_status_company_active())->get();
		$parentProject = Project::where('client_id', \globals::get_client_id())->where('parent', 0)->where('project_status', \globals::set_project_status_active())->get();

		return view('adminuser.project.recyclebin', compact('project', 'company', 'parentProject'));
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
				$details = [
					'project_name' => Project::where('project_id', $project_id)->value('project_name'),
					'url' => route('project.download-project', $project_id),
					'receiver' => Auth::user()->name,
				];
				\Mail::to(Auth::user()->email)->send(new \App\Mail\ClosingProject($details));

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

	public function download_project($id) {
		try {
			$project_client_id = Project::where('project_id', $id)->value('client_id');

			if ((Auth::user()->client_id == $project_client_id) && ((Project::where('project_id', $id)->value('user_id') == Auth::user()->user_id) || (ClientUser::where('user_id', Auth::user()->user_id)->where('client_id', $project_client_id)->value('role') == '0')) && Carbon::parse(Project::where('project_id', $id)->value('updated_at'))->greaterThanOrEqualTo(Carbon::now()->subDays(7))) {
				$folderName = 'uploads/'. $project_client_id . '/' . $id;
				$files = Storage::allFiles($folderName);
				$tempZipFile = tempnam(sys_get_temp_dir(), 'folder_zip');
				$zip = new ZipArchive();
				$zip->open($tempZipFile, ZipArchive::CREATE);
				$fileName = "";
				$fileFolder = "";
				$log = '';

				foreach ($files as $file) {
					$index = '';

					$relativePath = substr($file, strlen($folderName) + 1);

					$Path = explode('/', $relativePath);
					array_pop($Path);
					$Path = implode('/', $Path);

					$basenameFile = explode('/', $relativePath);
					$basenameFile = end($basenameFile);

					if (UploadFile::where('basename', $basenameFile)->value('status') == '1') {
						
						$pathFile = UploadFile::where('basename', $basenameFile)->value('directory');

						$originPath = implode('/', array_slice(explode('/', UploadFile::where('basename', $basenameFile)->value('directory')), 0, 4));
						foreach(array_slice(explode('/', UploadFile::where('basename', $basenameFile)->value('directory')), 4) as $path) {
							$originPath .= '/' . $path;
							$index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
						}
						
						$index .= DB::table('upload_files')->where('basename', basename($basenameFile))->value('index');
						$basenameFile = $index . ' - ' .UploadFile::where('basename', $basenameFile)->value('name');

						if ($Path == "") {
							$fixedPath = $basenameFile;
						} else {
							$FullPath = '';
							$OriginFullPath = $folderName;

							foreach(explode('/', $Path) as $paths) {
								$index = '';
								$OriginFullPath .= '/' . $paths;
								$originPath = implode('/', array_slice(explode('/', UploadFolder::where('directory', $OriginFullPath)->value('parent')), 0, 4));

								foreach(array_slice(explode('/', UploadFolder::where('directory', $OriginFullPath)->value('parent')), 4) as $path) {
									$originPath .= '/' . $path;
									
									$index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
								}

								$index .= DB::table('upload_folders')->where('directory', $OriginFullPath)->value('index');
								
								$subfolderName = is_null(Subproject::where('subproject_id', $paths)->value('subproject_name')) ? $paths : Subproject::where('subproject_id', $paths)->value('subproject_name');
								$Path = $index == "" ? 'Subproject '. $subfolderName : $index . ' - ' . $subfolderName;
								$log .= $OriginFullPath;
								$FullPath .= $Path . '/';
							}
							$fixedPath =  $FullPath . $basenameFile;
						}  
						
						$zip->addFile(Storage::path($file), $fixedPath);
					}
				}

				$zip->close();
				
				$destinationPath = 'downloads/'. Auth::user()->user_id . '/temp.zip'; 
				Storage::put($destinationPath, file_get_contents($tempZipFile));

				$desc = Auth::user()->name . " downloaded folder path ";
				\log::create(request()->all(), "success", $desc);

				return Storage::disk('local')->download($destinationPath, 'Project ' . Project::where('project_id',basename($folderName))->value('project_name') . '.zip');

			} else {
				return abort(404);
			}
		} catch (\Exception $e) {
			return abort(404);
		}
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

	public function recover_subproject(Request $request) {
		try {
			$id = $request->input('subprojectID');

			if (Auth::user()->type != \globals::set_role_administrator()) {
				return abort(404);
			} else {
				SubProject::where('subproject_id', $id)->update(['subproject_status' => '1']);

				$desc = Auth::user()->name." has recovered sub project ".$projname;
				\log::create(request()->all(), "success", $desc);

				$notification = "Subproject recovered!";
			}
		} catch (\Exception $e) {
			\log::create(request()->all(), "error", $e->getMessage());
			Alert::error('Error', $e->getMessage());
			return back();
		}
	}

	public function permanent_delete_subproject(Request $request) {
		try {
			$id = $request->input('subprojectID');

			if (Auth::user()->type != \globals::set_role_administrator()) {
				return abort(404);
			} else {
				SubProject::where('subproject_id', $id)->update(['subproject_status' => '2']);

				$desc = Auth::user()->name." has recovered sub project ".$projname;
				\log::create(request()->all(), "success", $desc);

				$notification = "Subproject recovered!";
			}
		} catch (\Exception $e) {
			\log::create(request()->all(), "error", $e->getMessage());
			Alert::error('Error', $e->getMessage());
			return back();
		}
	}

	public function delete_sub_project($id)
	{
		try {
			\DB::beginTransaction();

			$deleted = SubProject::where('subproject_id', $id)->update([
				'subproject_status' => '0', 
				'updated_by' => Auth::user()->id,
			]);
			if ($deleted) {
				AssignProject::where('subproject_id', $id)->delete();
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
