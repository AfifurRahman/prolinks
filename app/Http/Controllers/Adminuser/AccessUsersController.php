<?php

namespace App\Http\Controllers\Adminuser;

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
                    $existingUser = ClientUser::where('email_address', $email)->where('status', '!=', 3)->first();
                    
                    if (!$existingUser) {
                        $userID = Str::uuid(4);
                        $clientID = DB::table('clients')->where('client_email',Auth::user()->email)->value('client_id');
                        ClientUser::create([
                            'user_id'=> $userID,
                            'email_address' => $email,
                            'company' => '-',
                            'client_id' => \globals::get_client_id(),
                            'role' => $request->role,
                            'created_by' => Auth::user()->id,
                            'group_id' => 0,
                        ]);
        
                        $users = new User;
                        $users->client_id = \globals::get_client_id();
                        $users->user_id = $userID;
                        $users->name = "null";
                        $users->email = $email;
                        $users->type = $request->role;
                        $users->password = Hash::make(bcrypt(Str::random(255)));
                        $users->avatar_color = $this->get_random_avatar_color();
                        $users->save();

                        if(!empty($request->input('group')) && count($request->input('group')) > 0){
                            foreach ($request->input('group') as $key => $grup) {
                                $groups = new AssignUserGroup;
                                $groups->client_id = \globals::get_client_id();
                                $groups->group_id = $grup;
                                $groups->user_id = $users->user_id;
                                $groups->email = $users->email;
                                $groups->created_by = Auth::user()->id;
                                $groups->save();
                            }
                        }
                        
                        if(!empty($request->input('project')) && count($request->input('project')) > 0){
                            foreach ($request->input('project') as $key => $proj) {
                                $projects = new AssignProject;
                                $projects->client_id = \globals::get_client_id();
                                $projects->project_id = SubProject::where('subproject_id', $proj)->value('project_id');
                                $projects->subproject_id = $proj;
                                $projects->user_id = $users->user_id;
                                $projects->email = $users->email;
                                $projects->created_by = Auth::user()->id;
                                $projects->save();
                            }
                        }
        
                        $token = Password::getRepository()->create($users);
                        $details = [
                            'client_name' => $email,
                            'link' => URL::to('/create-password') . '/' . $token . '?email=' . str_replace("@", "%40", $email),
                        ];
        
                        $sendMail = \Mail::to($users->email)->send(new \App\Mail\CreateAdminClientPassword($details));

                        if ($sendMail) {
                          User::where('id', $users->id)->update([
                              'remember_token' => $token
                          ]);
                        }

                        $notification = "User invited";
                    } else {
                        $notification = "User already exist!";
                    };
                } else {
                    $notification = "Invalid email";
                }
            };

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $notification = "Can't invite user";
        }

        Session::flash('notification', $notification);
        return response()->json($notification);
        // return back()->with('notification', $notification);   
    }

    public function edit (Request $request, $user_id) {
        try {
            \DB::beginTransaction();

            $update1 = ClientUser::where('user_id', $user_id)->update([
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
                $notification = "User edited";
            }

            \DB::commit();
        } catch (\Exception $th) {
            \DB::rollBack();
            $notification = "Can't edit user";
        }

        return back()->with('notification', $notification);  
    }

    public function edit_role (Request $request, $user_id) {
        try {
            \DB::beginTransaction();
            
            ClientUser::where('user_id', $user_id)->update([
                'role' => $request->input('role'),
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            User::where('user_id', $user_id)->update([
                'type' => $request->input('role'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if($request->input('role') == \globals::set_role_administrator()){
                $deleteGroup = AssignUserGroup::where('client_id', \globals::get_client_id())->where('user_id', $user_id)->delete();
                $deleteProjects = AssignProject::where('client_id', \globals::get_client_id())->where('user_id', $user_id)->delete();
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
                        $projects->email = User::where('user_id', $user_id)->value('email');
                        $projects->created_by = Auth::user()->id;
                        $projects->save();
                    }
                }
            }

            $notification = "Role edited";

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            Alert::error("Error", $e->getMessage());
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
                $notification = "Group edited";
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $notification = "Can't edit group";
        }

        return back()->with('notification', $notification); 
    }

    public function move_group(Request $request)
    {
        try {
            \DB::beginTransaction();

            $user_id = $request->user_id;
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

            $notification = "User moved successfully";

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $notification = "Failed to move user";
        }

        return back()->with('notification', $notification);
    }

    public function resend_email($encodedEmail)
    {
        try {
            $email = base64_decode($encodedEmail);

            $users = User::where('email', $email)->firstOrFail();

            $token = Password::getRepository()->create($users);

            $details = [
                'client_name' => $email,
                'link' => URL::to('/create-password') . '/' . $token . '?email=' . str_replace("@", "%40", $email),
            ];

            \Mail::to($users->email)->send(new \App\Mail\CreateAdminClientPassword($details));

            $notification = "Email sent";
        } catch(\Exception $e) {
            $notification = "Failed to send email";
        }

        return back()->with('notification', $notification);
    }

    public function disable_user($encodedEmail)
    {
        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
        try {
            \DB::beginTransaction();

            $email = base64_decode($encodedEmail);
            $update1 = ClientUser::where('email_address',$email)->update([
                'status' => 2,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $update2 = User::where('email',$email)->update([
                'status' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($update1 && $update2) {
                $notification = "User has been disabled";
            }
            
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            $notification = "Failed to disable user";
        }

        return back()->with('notification', $notification);
    }
    public function enable_user($encodedEmail)
    {
        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
        try {
            \DB::beginTransaction();

            $email = base64_decode($encodedEmail);
            $update1 = ClientUser::where('email_address',$email)->update([
                'status' => 1,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $update2 = User::where('email',$email)->update([
                'status' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($update1 && $update2) {
                $notification = "User has been enabled";
            }
            
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            $notification= "Failed to enable user";
        }
       
        return back()->with('notification', $notification);
    }

    public function delete_group($group_id) {
        try {
            \DB::beginTransaction();

            /* status group : 0 => deleted, 1 => active, 2 => Disabeld */
            AccessGroup::where('group_id', $group_id)->update([
                'group_status' => 0,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $notification = "Group has been deleted";

            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            $notification= "Failed to delete group";
        }
       
        return back()->with('notification', $notification);
    }

    public function disabled_group($group_id) {
        /* status group : 0 => deleted, 1 => active, 2 => Disabeld */
        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
        try {
            \DB::beginTransaction();

            AccessGroup::where('group_id', $group_id)->update([
                'group_status' => 2,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $checkAssignGroup = AssignUserGroup::where('group_id', $group_id)->get();
            if (count($checkAssignGroup) > 0) {
                foreach($checkAssignGroup as $group){
                    User::where('user_id', $group->user_id)->update([
                        'status' => 0
                    ]);

                    ClientUser::where('user_id', $group->user_id)->update([
                        'status' => 2
                    ]);
                }
            }

            $notification = "Group has been disabled";

            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            $notification= "Failed to disabled group";
        }
       
        return back()->with('notification', $notification);
    }

    public function enable_group($group_id) {
        /* status group : 0 => deleted, 1 => active, 2 => Disabeld */
        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
        try {
            \DB::beginTransaction();

            AccessGroup::where('group_id', $group_id)->update([
                'group_status' => 1,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $checkAssignGroup = AssignUserGroup::where('group_id', $group_id)->get();
            if (count($checkAssignGroup) > 0) {
                foreach($checkAssignGroup as $group){
                    User::where('user_id', $group->user_id)->update([
                        'status' => 1
                    ]);

                    ClientUser::where('user_id', $group->user_id)->update([
                        'status' => 1
                    ]);
                }
            }

            $notification = "Group has been enable";

            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            $notification= "Failed to enable group";
        }
       
        return back()->with('notification', $notification);
    }

    public function delete_user($encodedEmail)
    {
        /* status client user : 0 => invite, 1 => active, 2 => Disabeld, 3 => deleted */
        try {
            \DB::beginTransaction();

            $email = base64_decode($encodedEmail);
            
            $update1 = ClientUser::where('email_address',$email)->update(['status' => 3]);
            
            $getUsers = User::where('email',$email)->first();
            if (!empty($getUsers->id)) {
                $trashUsers = new TrashUsers;
                $trashUsers->user_id = $getUsers->user_id;
                $trashUsers->client_id = $getUsers->client_id;
                $trashUsers->name = $getUsers->name;
                $trashUsers->username = $getUsers->username;
                $trashUsers->email = $getUsers->email;
                $trashUsers->email_verified_at = $getUsers->email_verified_at;
                $trashUsers->password = $getUsers->password;
                $trashUsers->type = $getUsers->type;
                $trashUsers->status = $getUsers->status;
                $trashUsers->avatar_color = $getUsers->avatar_color;
                $trashUsers->session_project = $getUsers->session_project;
                $trashUsers->last_signed = $getUsers->last_signed;
                $trashUsers->created_at = date('Y-m-d H:i:s');
                if ($trashUsers->save()) {
                    AssignUserGroup::where('user_id', $trashUsers->user_id)->delete();
                }
            }
            
            $deleted = User::where('email',$email)->delete();
            
            if ($update1 && $deleted) {
                $notification = "User has been deleted";
            }
            
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
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
                $notification = 'success create group';
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
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