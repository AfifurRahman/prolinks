<?php

namespace App\Http\Controllers\Adminuser;

use App\Models\SettingEmailNotification;
use App\Models\SubProject;
use App\Models\TrashUsers;
use Auth;
use App\Models\User;
use App\Models\ClientUser;
use App\Models\Company;
use App\Models\AccessGroup;
use App\Models\Project;
use App\Models\AssignProject;
use App\Models\AssignUserGroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Session;
use Alert;

class AccessUsersController extends Controller
{
    public function index()
    {
        if (Auth::user()->type != \globals::set_role_administrator()) {
            return abort(404);
        }

        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */

        $adminusercompany = DB::table('clients')->where('client_email',Auth::user()->email)->value('client_id');
        $clientuser = ClientUser::orderBy('group_id', 'ASC')->where('client_id', \globals::get_client_id())->where('user_id', '!=', Auth::user()->user_id)->whereIn('status', [0, 1, 2])->orderBy('id', 'DESC')->get();
        $group = AccessGroup::where('client_id', \globals::get_client_id())->where('group_status', 1)->orderBy('id', 'DESC')->get();
        $project = Project::where('client_id', \globals::get_client_id())->where('project_status', 1)->get();
        $owners = User::where('client_id', \globals::get_client_id())->where('type', 0)->where('user_id', Auth::user()->user_id)->get();
        $listGroup = AccessGroup::where('client_id', \globals::get_client_id())->whereIn('group_status', [1,2])->get();

        return view('adminuser.users.index', compact('clientuser','group','owners', 'listGroup', 'project'));
    }

    public function detail($user_id){
        if (Auth::user()->type != \globals::set_role_administrator()) {
            return abort(404);
        }

        $clientuser = ClientUser::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->whereIn('status', [0, 1, 2])->first();
        if(empty($clientuser->id)){
            return redirect(route('adminuser.access-users.list', 'tab=user'));
        }

        $group = AccessGroup::where('client_id', \globals::get_client_id())->where('group_status', 1)->get();
        $project = Project::where('client_id', \globals::get_client_id())->where('project_status', 1)->get();
        $groupDetail = AssignUserGroup::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->pluck('group_id')->toArray();
        $projectDetail = AssignProject::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->pluck('subproject_id')->toArray();

        return view('adminuser.users.detail', compact('clientuser', 'group', 'project', 'groupDetail', 'projectDetail'));
    }

    public function detail_group($group_id){
        if (Auth::user()->type != \globals::set_role_administrator()) {
            return abort(404);
        }
        
        $group = AccessGroup::where('group_id', $group_id)->where('client_id', \globals::get_client_id())->where('group_status', 1)->first();
        if(empty($group->id)){
            return redirect(route('adminuser.access-users.list', 'tab=group'));
        }

        $member = AssignUserGroup::where('client_id', \globals::get_client_id())->where('group_id', $group_id)->get();
        return view('adminuser.users.detail_group', compact('group', 'member'));
    }

    public function create_user(Request $request)
    {
        try {
            \DB::beginTransaction();

            $emailAddresses = explode(',', $request->email_address);

            foreach ($emailAddresses as $email) {
                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    /* status client use : 3 => deleted */
                    $existingUser = ClientUser::where('email_address', $email)->where('status', '!=', 3)->where('client_id', \globals::get_client_id())->first();
                    
                    if (!$existingUser) {

                        /* if sub project doesnt exist */
                        $check_subproject = SubProject::where('client_id', \globals::get_client_id())->where('subproject_status', 1)->get();
                        if (count($check_subproject) > 0) {
                            $existingAccess = User::where('email', $email)->first();

                            $userID = "";
                            $userName = "";
                            if (empty($existingAccess->id)) {
                                $userID = Str::uuid(4);
                                $userName = "";
                            }else{
                                $userID = $existingAccess->user_id;
                                $userName = $existingAccess->name;
                            }

                            $client_user = new ClientUser;
                            $client_user->user_id = $userID;
                            $client_user->email_address = $email;
                            $client_user->name = $userName;
                            $client_user->company = '-';
                            $client_user->client_id = \globals::get_client_id();
                            $client_user->role = $request->role;
                            $client_user->created_by = Auth::user()->id;
                            $client_user->group_id = 0;
                            $client_user->save();

                            $newIDUsers = "";
                            if (empty($existingAccess->id)) {
                                $users = new User;
                                $users->client_id = \globals::get_client_id();
                                $users->user_id = $userID;
                                $users->name = "null";
                                $users->email = $email;
                                $users->type = $request->role;
                                $users->password = Hash::make(bcrypt(Str::random(255)));
                                $users->avatar_color = $this->get_random_avatar_color();
                                if ($users->save()) {
                                    $newIDUsers = $users->id;
                                }
                            }

                            if(!empty($request->input('group')) && count($request->input('group')) > 0){
                                foreach ($request->input('group') as $key => $grup) {
                                    $groups = new AssignUserGroup;
                                    $groups->client_id = \globals::get_client_id();
                                    $groups->group_id = $grup;
                                    $groups->user_id = $userID;
                                    $groups->email = $email;
                                    $groups->created_by = Auth::user()->id;
                                    $groups->save();
                                }
                            }

                            if ($request->role == \globals::set_role_administrator()) {
                                $get_project = Project::where('client_id', \globals::get_client_id())->where('project_status', 1)->get();
                                if (count($get_project) > 0) {
                                    foreach ($get_project as $key => $proj) {
                                        if (count($proj->RefSubProject) > 0) {
                                            foreach ($proj->RefSubProject as $key => $subproj) {
                                                $projects = new AssignProject;
                                                $projects->client_id = \globals::get_client_id();
                                                $projects->project_id = $subproj->project_id;
                                                $projects->subproject_id = $subproj->subproject_id;
                                                $projects->user_id = $userID;
                                                $projects->clientuser_id = $client_user->id;
                                                $projects->email = $email;
                                                $projects->created_by = Auth::user()->id;
                                                $projects->save();

                                                $settings = new SettingEmailNotification;
                                                $settings->client_id = $projects->client_id;
                                                $settings->user_id = $projects->user_id;
                                                $settings->project_id = $projects->project_id;
                                                $settings->subproject_id = $projects->subproject_id;
                                                $settings->clientuser_id = $projects->clientuser_id;
                                                $settings->created_by = $projects->created_by;
                                                $settings->created_at = date('Y-m-d H:i:s');
                                                $settings->save();
                                            } 
                                        }
                                    }
                                }
                            }else{
                                if(!empty($request->input('project')) && count($request->input('project')) > 0){
                                    foreach ($request->input('project') as $key => $proj) {
                                        $projects = new AssignProject;
                                        $projects->client_id = \globals::get_client_id();
                                        $projects->project_id = SubProject::where('subproject_id', $proj)->value('project_id');
                                        $projects->subproject_id = $proj;
                                        $projects->user_id = $userID;
                                        $projects->clientuser_id = $client_user->id;
                                        $projects->email = $email;
                                        $projects->created_by = Auth::user()->id;
                                        $projects->save();

                                        $settings = new SettingEmailNotification;
                                        $settings->client_id = $projects->client_id;
                                        $settings->user_id = $projects->user_id;
                                        $settings->project_id = $projects->project_id;
                                        $settings->subproject_id = $projects->subproject_id;
                                        $settings->clientuser_id = $projects->clientuser_id;
                                        $settings->created_by = $projects->created_by;
                                        $settings->created_at = date('Y-m-d H:i:s');
                                        $settings->save();
                                    }
                                }
                            }

                            $token = "";
                            if (!empty($existingAccess->id)) {
                                $token = $existingAccess->remember_token;
                                $session_project = AssignProject::where('user_id', $userID)->where('client_id', \globals::get_client_id())->orderBy('id', 'DESC')->value('subproject_id');
                                $types = AssignProject::join('client_users', 'client_users.id', 'assign_project.clientuser_id')->where('assign_project.subproject_id', $session_project)->where('assign_project.user_id', $userID)->value('role');
                                $client_id = AssignProject::join('client_users', 'client_users.id', 'assign_project.clientuser_id')->where('assign_project.subproject_id', $session_project)->where('assign_project.user_id', $userID)->value('client_users.client_id');
                                User::where('user_id', $userID)->update([
                                    'client_id' => $client_id,
                                    'session_project' => $session_project,
                                    'type' => $types
                                ]);
                            }elseif (empty($existingAccess->id)) {
                                $token = Password::getRepository()->create($users);
                            }
                            
                            $link = URL::to('/create-password') . '/' . $token . '?email=' . str_replace("@", "%40", $email);
                            $existAccount = "";
                            if (!empty($existingAccess->id)) {
                                // $link = URL::to('/login');
                                $existAccount = "YES";
                            }else{
                            $existAccount = "NO";
                            }

                            $details = [
                                'client_name' => $email,
                                'exist_account' => $existAccount,
                                'link' => $link
                            ];
            
                            $sendMail = \Mail::to($email)->send(new \App\Mail\CreateAdminClientPassword($details));

                            if ($sendMail) {
                                if (empty($existingAccess->id)) {
                                    User::where('id', $newIDUsers)->update([
                                        'remember_token' => $token
                                    ]);
                                }
                            }

                            $role_name = "";
                            if ($request->role == 0) {
                                $role_name = "Administrator";
                            }elseif ($request->role == 1) {
                                $role_name = "Collaborator";
                            }elseif ($request->role == 2) {
                                $role_name = "Reviewer";
                            }

                            $desc = Auth::user()->name." invited ".$email." as ".$role_name;
                            \log::create($request->all(), "success", $desc);
                            $notification = "User invited";

                        }else{
                            $notification = "failed invitation, please add a subproject before invite users";
                            \log::create($request->all(), "error", $notification);
                        }
                    } else {
                        $notification = "User already exist!";
                        \log::create($request->all(), "error", $notification);
                    };
                } else {
                    $notification = "Invalid email";
                    \log::create($request->all(), "error", $notification);
                }
            };

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            \log::create($request->all(), "error", $e->getMessage());
            $notification = "Failed invited";
        }

        Session::flash('notification', $notification);
        return response()->json($notification); 
    }

    public function edit (Request $request, $user_id) {
        try {
            \DB::beginTransaction();

            $update1 = ClientUser::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->update([
                'name'=> $request->name,
                'company'=> $request->company,
                'job_title'=> $request->job_title,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $update2 = User::where('user_id', $user_id)->update([
                'name'=> $request->name,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($update1 && $update2) {
                $desc = Auth::user()->name." has been edited user ".$request->name;
				\log::create($request->all(), "success", $desc);
                $notification = "User edited";
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            \log::create($request->all(), "error", $e->getMessage());
            $notification = "Can't edit user";
        }

        return back()->with('notification', $notification);  
    }

    public function selfedit (Request $request) {
        try {
            \DB::beginTransaction();

            ClientUser::where('user_id', Auth::user()->user_id)->update([
                'name' => $request->input('name'),
                'job_title' => $request->input('job_title'),
            ]);

            User::where('user_id', Auth::user()->user_id)->update([
                'phone' => $request->input('phone'),
                'name' => $request->input('name'),
                'title' => $request->input('job_title'),
            ]);

            \DB::commit();

            return redirect()->route('setting', 'tab=account_setting');
        } catch (\Exception $e) {
            return back();
        }
    }

    public function upload_picture (Request $request) {
        try {
            \DB::beginTransaction();
        
        
            $request->validate([
                'image' => 'required|mimes:jpg,jpeg|max:2048',
            ]);
        
            // Retrieve the uploaded image
            $image = $request->file('image');
        
            // Define a file name with extension
            $fileName = Auth::user()->user_id . '.' . $image->getClientOriginalExtension();
        
            // Store the image
            $image->storeAs('/', $fileName, 'avatar');
        
            // Update the user's avatar image path
            User::where('user_id', Auth::user()->user_id)->update(['avatar_image' => $fileName]);
        
            \DB::commit();
        
            return back();
        } catch (\Exception $e) {
            return back();
        }
    }

    public function edit_role (Request $request, $user_id) {
        try {
            \DB::beginTransaction();
            
            ClientUser::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->update([
                'role' => $request->input('role'),
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // User::where('user_id', $user_id)->update([
            //     'type' => $request->input('role'),
            //     'updated_at' => date('Y-m-d H:i:s')
            // ]);

            if($request->input('role') == \globals::set_role_administrator()){
                $deleteGroup = AssignUserGroup::where('client_id', \globals::get_client_id())->where('user_id', $user_id)->delete();
                $deleteProjects = AssignProject::where('client_id', \globals::get_client_id())->where('user_id', $user_id)->delete();
                
                $get_project = Project::where('client_id', \globals::get_client_id())->where('project_status', 1)->get();
                if (count($get_project) > 0) {
                    foreach ($get_project as $key => $proj) {
                        if (count($proj->RefSubProject) > 0) {
                            foreach ($proj->RefSubProject as $key => $subproj) {
                                $projects = new AssignProject;
                                $projects->client_id = \globals::get_client_id();
                                $projects->project_id = $subproj->project_id;
                                $projects->subproject_id = $subproj->subproject_id;
                                $projects->user_id = $user_id;
                                $projects->clientuser_id = ClientUser::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->value('id');
                                $projects->email = ClientUser::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->value('email_address');
                                $projects->created_by = Auth::user()->id;
                                $projects->save();
                            }
                        }
                    }
                }

            }elseif($request->input('role') == \globals::set_role_collaborator()){
                $deleteGroup = AssignUserGroup::where('client_id', \globals::get_client_id())->where('user_id', $user_id)->delete();
                if(!empty($request->input('project')) && count($request->input('project')) > 0){
                    $deleteProjects = AssignProject::where('client_id', \globals::get_client_id())->where('user_id', $user_id)->delete();
                    foreach ($request->input('project') as $key => $proj) {
                        $projects = new AssignProject;
                        $projects->client_id = \globals::get_client_id();
                        $projects->project_id = SubProject::where('subproject_id', $proj)->value('project_id');
                        $projects->subproject_id = $proj;
                        $projects->user_id = $user_id;
                        $projects->clientuser_id = ClientUser::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->value('id');
                        $projects->email = User::where('user_id', $user_id)->value('email');
                        $projects->created_by = Auth::user()->id;
                        $projects->save();
                    }
                }
            }else{
                if(!empty($request->input('group')) && count($request->input('group')) > 0){
                    $deleteGroup = AssignUserGroup::where('client_id', \globals::get_client_id())->where('user_id', $user_id)->delete();
                    foreach ($request->input('group') as $key => $grup) {
                        $groups = new AssignUserGroup;
                        $groups->client_id = \globals::get_client_id();
                        $groups->group_id = $grup;
                        $groups->user_id = $user_id;
                        $groups->email = User::where('user_id', $user_id)->value('email');
                        $groups->created_by = Auth::user()->id;
                        $groups->save();
                    }
                }
                
                if(!empty($request->input('project')) && count($request->input('project')) > 0){
                    $deleteProjects = AssignProject::where('client_id', \globals::get_client_id())->where('user_id', $user_id)->delete();
                    foreach ($request->input('project') as $key => $proj) {
                        $projects = new AssignProject;
                        $projects->client_id = \globals::get_client_id();
                        $projects->project_id = SubProject::where('subproject_id', $proj)->value('project_id');
                        $projects->subproject_id = $proj;
                        $projects->user_id = $user_id;
                        $projects->clientuser_id = ClientUser::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->value('id');
                        $projects->email = User::where('user_id', $user_id)->value('email');
                        $projects->created_by = Auth::user()->id;
                        $projects->save();
                    }
                }
            }

            $role_name = "";
            if ($request->input('role') == 0) {
                $role_name = "Administrator";
            }elseif ($request->input('role') == 1) {
                $role_name = "Collaborator";
            }elseif ($request->input('role') == 2) {
                $role_name = "Reviewer";
            }

            $name_user = ClientUser::where('user_id', $user_id)->value('name');
            $desc = Auth::user()->name." has been edited ".$name_user." as ".$role_name;
			\log::create($request->all(), "success", $desc);
            $notification = "Role edited";

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            \log::create($request->all(), "error", $e->getMessage());
            $notification = "failed edite role";
            return back();
        }

        return back()->with('notification', $notification);  
    }

    public function edit_group(Request $request, $group_id) {
        try {
            \DB::beginTransaction();

            $update = AccessGroup::where('client_id', \globals::get_client_id())->where('group_id', $group_id)->update([
                'group_desc'=> $request->group_desc,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($update) {
                $desc = Auth::user()->name." has been edited group";
                \log::create($request->all(), "success", $desc);
                $notification = "Group edited";
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            \log::create($request->all(), "error", $e->getMessage());
            $notification = "Can't edit group";
        }

        return back()->with('notification', $notification); 
    }

    public function move_group(Request $request)
    {
        try {
            \DB::beginTransaction();

            $user_id = $request->user_id;
            $group_name = [];
            if(!empty($request->input('group')) && count($request->input('group')) > 0){
                $deleteGroup = AssignUserGroup::where('client_id', \globals::get_client_id())->where('user_id', $user_id)->delete();
                foreach ($request->input('group') as $key => $grup) {
                    $group_name[] = AccessGroup::where('group_id', $grup)->value('group_name');

                    $groups = new AssignUserGroup;
                    $groups->client_id = \globals::get_client_id();
                    $groups->group_id = $grup;
                    $groups->user_id = $user_id;
                    $groups->email = User::where('user_id', $user_id)->value('email');
                    $groups->created_by = Auth::user()->id;
                    $groups->save();
                }
            }

            $name_user = ClientUser::where('user_id', $user_id)->value('name');
            $desc = Auth::user()->name." moved user ".$name_user. " in group ".json_encode($group_name);
            \log::create($request->all(), "success", $desc);
            $notification = "User moved successfully";
            

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            \log::create($request->all(), "error", $e->getMessage());
            $notification = "Failed to move user";
        }

        return back()->with('notification', $notification);
    }

    public function resend_email($encodedEmail)
    {
        try {
            $email = base64_decode($encodedEmail);

            /* check if users has created password */
            $users = User::where('email', $email)->where('password_created', 1)->first();

            $token = "";
            if (!empty($users->id)) {
                $token = $users->remember_token;
            }else{
                $users = User::where('email', $email)->firstOrFail();
                $token = Password::getRepository()->create($users);
                User::where('email', $email)->update([
                    'remember_token' => $token,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            User::where('email', $email)->update(['remember_token' => $token]);

            $details = [
                'client_name' => $email,
                'link' => URL::to('/create-password') . '/' . $token . '?email=' . str_replace("@", "%40", $email),
            ];

            \Mail::to($users->email)->send(new \App\Mail\CreateAdminClientPassword($details));

            $desc = Auth::user()->name." resend email invitation to ".$email;
			\log::create(request()->all(), $details, $desc);
            $notification = "Email sent";
        } catch(\Exception $e) {
            \log::create(request()->all(), "error", $e->getMessage());
            $notification = "Failed to send email";
        }

        return back()->with('notification', $notification);
    }

    public function disable_user($encodedID)
    {
        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
        try {
            \DB::beginTransaction();

            $id = base64_decode($encodedID);
            $update1 = ClientUser::where('id',$id)->where('client_id', \globals::get_client_id())->update([
                'status' => 2,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($update1) {
                $getUsers = ClientUser::where('id',$id)->where('client_id', \globals::get_client_id())->first();
                if (!empty($getUsers->id)) {
                    AssignProject::where('clientuser_id', $getUsers->id)->where('client_id', $getUsers->client_id)->update([
                        'deleted' => 1
                    ]);
                    
                    $session_project = AssignProject::where('user_id', $getUsers->user_id)->orderBy('id', 'DESC')->value('subproject_id');
                    $types = AssignProject::join('client_users', 'client_users.id', 'assign_project.clientuser_id')->where('assign_project.subproject_id', $session_project)->where('assign_project.user_id', $getUsers->user_id)->value('role');
                    $client_id = AssignProject::join('client_users', 'client_users.id', 'assign_project.clientuser_id')->where('assign_project.subproject_id', $session_project)->where('assign_project.user_id', $getUsers->user_id)->value('client_users.client_id');
                    User::where('email', $getUsers->email_address)->update([
                        'client_id' => $client_id,
                        'session_project' => $session_project,
                        'type' => $types
                    ]);

                    $name_user = ClientUser::where('email_address',$getUsers->email_address)->where('client_id', \globals::get_client_id())->value('name');
                    $desc = Auth::user()->name." has been disabled ".$name_user;
                    \log::create(request()->all(), "success", $desc);

                    $notification = "User has been disabled";
                }
            }
            
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();

            \log::create(request()->all(), "error", $e->getMessage());
            $notification = "Failed to disable user";
        }

        return back()->with('notification', $notification);
    }
    public function enable_user($encodedID)
    {
        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
        try {
            \DB::beginTransaction();

            $id = base64_decode($encodedID);
            $update1 = ClientUser::where('id',$id)->where('client_id', \globals::get_client_id())->update([
                'status' => 1,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($update1) {
                $getUsers = ClientUser::where('id',$id)->where('client_id', \globals::get_client_id())->first();
                if (!empty($getUsers->id)) {
                    AssignProject::where('clientuser_id', $getUsers->id)->where('client_id', $getUsers->client_id)->update([
                        'deleted' => 0
                    ]);
                    
                    $session_project = AssignProject::where('user_id', $getUsers->user_id)->orderBy('id', 'DESC')->value('subproject_id');
                    $types = AssignProject::join('client_users', 'client_users.id', 'assign_project.clientuser_id')->where('assign_project.subproject_id', $session_project)->where('assign_project.user_id', $getUsers->user_id)->value('role');
                    $client_id = AssignProject::join('client_users', 'client_users.id', 'assign_project.clientuser_id')->where('assign_project.subproject_id', $session_project)->where('assign_project.user_id', $getUsers->user_id)->value('client_users.client_id');
                    User::where('email', $getUsers->email_address)->update([
                        'client_id' => $client_id,
                        'session_project' => $session_project,
                        'type' => $types
                    ]);

                    $name_user = ClientUser::where('email_address',$getUsers->email_address)->where('client_id', \globals::get_client_id())->value('name');
                    $desc = Auth::user()->name." has been enabled ".$name_user;
                    \log::create(request()->all(), "success", $desc);
                    $notification = "User has been enabled";
                }
            }
            
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();

            \log::create(request()->all(), "error", $e->getMessage());
            $notification= "Failed to enable user";
        }
       
        return back()->with('notification', $notification);
    }

    public function delete_group($group_id) {
        try {
            \DB::beginTransaction();

            /* status group : 0 => deleted, 1 => active, 2 => Disabeld */
            AccessGroup::where('group_id', $group_id)->where('client_id', \globals::get_client_id())->update([
                'group_status' => 0,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $group_name = AccessGroup::where('group_id', $group_id)->where('client_id', \globals::get_client_id())->value('group_name');
            $desc = Auth::user()->name." deleted the group ".$group_name;
			\log::create(request()->all(), "success", $desc);
            $notification = "Group has been deleted";

            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();

            \log::create(request()->all(), "error", $e->getMessage());
            $notification= "Failed to delete group";
        }
       
        return back()->with('notification', $notification);
    }

    public function disabled_group($group_id) {
        /* status group : 0 => deleted, 1 => active, 2 => Disabeld */
        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
        try {
            \DB::beginTransaction();

            AccessGroup::where('group_id', $group_id)->where('client_id', \globals::get_client_id())->update([
                'group_status' => 2,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $checkAssignGroup = AssignUserGroup::where('group_id', $group_id)->where('client_id', \globals::get_client_id())->get();
            if (count($checkAssignGroup) > 0) {
                foreach($checkAssignGroup as $group){
                    // User::where('user_id', $group->user_id)->update([
                    //     'status' => 0
                    // ]);

                    // ClientUser::where('user_id', $group->user_id)->update([
                    //     'status' => 2
                    // ]);
                }
            }

            $group_name = AccessGroup::where('group_id', $group_id)->where('client_id', \globals::get_client_id())->value('group_name');
            $desc = Auth::user()->name." disabled the group ".$group_name;
			\log::create(request()->all(), "success", $desc);
            $notification = "Group has been disabled";

            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();

            \log::create(request()->all(), "error", $e->getMessage());
            $notification= "Failed to disabled group";
        }
       
        return back()->with('notification', $notification);
    }

    public function enable_group($group_id) {
        /* status group : 0 => deleted, 1 => active, 2 => Disabeld */
        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
        try {
            \DB::beginTransaction();

            AccessGroup::where('group_id', $group_id)->where('client_id', \globals::get_client_id())->update([
                'group_status' => 1,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $checkAssignGroup = AssignUserGroup::where('group_id', $group_id)->where('client_id', \globals::get_client_id())->get();
            if (count($checkAssignGroup) > 0) {
                foreach($checkAssignGroup as $group){
                    // User::where('user_id', $group->user_id)->update([
                    //     'status' => 1
                    // ]);

                    // ClientUser::where('user_id', $group->user_id)->update([
                    //     'status' => 1
                    // ]);
                }
            }

            $group_name = AccessGroup::where('group_id', $group_id)->where('client_id', \globals::get_client_id())->value('group_name');
            $desc = Auth::user()->name." enabled the group ".$group_name;
			\log::create(request()->all(), "success", $desc);
            $notification = "Group has been enabled";

            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();

            \log::create(request()->all(), "error", $e->getMessage());
            $notification= "Failed to enable group";
        }
       
        return back()->with('notification', $notification);
    }

    public function delete_user($encodedID)
    {
        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
        try {
            \DB::beginTransaction();

            $id = base64_decode($encodedID);
            
            $update1 = ClientUser::where('id',$id)->where('client_id', \globals::get_client_id())->update(['status' => 3]);
            
            $getUsers = ClientUser::where('id',$id)->where('client_id', \globals::get_client_id())->first();
            if (!empty($getUsers->id)) {
                AssignUserGroup::where('user_id', $getUsers->user_id)->where('client_id', $getUsers->client_id)->delete();
                AssignProject::where('clientuser_id', $getUsers->id)->where('client_id', $getUsers->client_id)->delete();
                
                $session_project = AssignProject::where('user_id', $getUsers->user_id)->orderBy('id', 'DESC')->value('subproject_id');
                $types = AssignProject::join('client_users', 'client_users.id', 'assign_project.clientuser_id')->where('assign_project.subproject_id', $session_project)->where('assign_project.user_id', $getUsers->user_id)->value('role');
                $client_id = AssignProject::join('client_users', 'client_users.id', 'assign_project.clientuser_id')->where('assign_project.subproject_id', $session_project)->where('assign_project.user_id', $getUsers->user_id)->value('client_users.client_id');
                User::where('email', $getUsers->email_address)->update([
                    'client_id' => $client_id,
                    'session_project' => $session_project,
                    'type' => $types
                ]);
            }
            
            // $deleted = User::where('email',$email)->delete();
            
            if ($update1) {
                $user_name = $getUsers->name;
                $desc = Auth::user()->name." deleted user ".$user_name;
                \log::create(request()->all(), "success", $desc);
                
                $notification = "User has been deleted";
            }
            
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();

            \log::create(request()->all(), "error", $e->getMessage());
            $notification= "Failed to deleted user";
        }
       
        return back()->with('notification', $notification);
    }

    public function create_group(Request $request) {
        try {
            \DB::beginTransaction();

            $group = new AccessGroup;
            $group->group_id = Str::uuid(4);
            $group->project_id = Session::get('project_id');
            $group->client_id = \globals::get_client_id();
            $group->user_id = Auth::user()->user_id;
            $group->group_name = $request->input('group_name');
            $group->group_desc = $request->input('group_desc');
            $group->created_by = Auth::user()->id;
            $group->created_at = date('Y-m-d H:i:s');
            if($group->save()) {
                $desc = Auth::user()->name." created group ".$request->input('group_name');
                \log::create($request->all(), "success", $desc);
                $notification = 'success create group';
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            \log::create($request->all(), "error", $e->getMessage());
            $notification = "Failed to create group";
        }

        return back()->with('notification', $notification);
    }

    private function get_random_avatar_color(){
        $color = [
            "#1570EF", "#12B76A", "#D92D20", "#FDB022", "#802b00", "#55552b", "#b30059", "#003366", "#8c1aff"
        ];

        $random_color = array_rand($color);
        return $color[$random_color];
    }
}