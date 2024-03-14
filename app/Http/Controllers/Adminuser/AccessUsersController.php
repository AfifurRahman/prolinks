<?php

namespace App\Http\Controllers\Adminuser;

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

class AccessUsersController extends Controller
{
    public function index()
    {
        $adminusercompany = DB::table('clients')->where('client_email',Auth::user()->email)->value('client_id');

        $clientuser = ClientUser::orderBy('group_id', 'ASC')->where('client_id', $adminusercompany)->get();
        $group = AccessGroup::where('client_id', $adminusercompany)->pluck('group_id')->toArray();
        $project = Project::where('client_id', $adminusercompany)->pluck('project_id')->toArray();
        $owners = User::where('type', 0)->where('user_id', Auth::user()->user_id)->get();
        $listGroup = AccessGroup::where('client_id', $adminusercompany)->get();
        array_unshift($group, 0);
        array_unshift($project, 0);

        return view('adminuser.users.index', compact('clientuser','group','owners', 'listGroup', 'project'));
    }

    public function detail($user_id){
        $clientuser = ClientUser::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->firstOrFail();
        $group = AccessGroup::where('client_id', \globals::get_client_id())->pluck('group_id')->toArray();
        $project = Project::where('client_id', \globals::get_client_id())->pluck('project_id')->toArray();
        $groupDetail = AssignUserGroup::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->pluck('group_id')->toArray();
        $projectDetail = AssignProject::where('user_id', $user_id)->where('client_id', \globals::get_client_id())->pluck('project_id')->toArray();
        array_unshift($group, 0);
        array_unshift($project, 0);

        return view('adminuser.users.detail', compact('clientuser', 'group', 'project', 'groupDetail', 'projectDetail'));
    }

    public function detail_group($group_id){
        $group = AccessGroup::where('group_id', $group_id)->where('client_id', \globals::get_client_id())->first();
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
                    $existingUser = ClientUser::where('email_address', $email)->first();
                    
                    if (!$existingUser) {
                        $userID = Str::uuid(4);
                        ClientUser::create([
                            'user_id'=> $userID,
                            'email_address' => $email,
                            'company' => '-',
                            'client_id' => DB::table('clients')->where('client_email',Auth::user()->email)->value('client_id'),
                            'role' => $request->role,
                            // 'group_id' => $request->group,
                            'created_by' => Auth::user()->id,
                            'group_id' => 0,
                        ]);
        
                        $users = new User;
                        $users->user_id = $userID;
                        $users->name = Auth::user()->name;
                        $users->email = $email;
                        $users->type = $request->role;
                        $users->password = Hash::make(bcrypt(Str::random(255)));
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
                                $projects->project_id = $proj;
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
        return back()->with('notification', $notification);   
    }

    public function edit (Request $request, $user_id) {
        try {
            \DB::beginTransaction();

            $update = ClientUser::where('user_id', $user_id)->update([
                'name'=> $request->name,
                'company'=> $request->company,
                'job_title'=> $request->job_title,
            ]);

            if ($update) {
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

            // coming soon

            if ($update) {
                $notification = "Role edited";
            }

            \DB::commit();
        } catch (\Exception $th) {
            \DB::rollBack();
            $notification = "Can't edit role";
        }

        return back()->with('notification', $notification);  
    }

    public function edit_group(Request $request, $group_id) {
        try {
            \DB::beginTransaction();

            $update = AccessGroup::where('client_id', \globals::get_client_id())->where('group_id', $group_id)->update([
                'group_desc'=> $request->group_desc,
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
            $users = base64_decode($request->username);

            $group = $request->group_num;

            DB::table('client_users')->where('email_address', $users)->update(['group_id' => $group]);

            $notification = "User moved successfully";
        } catch (\Exception $e) {
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
        try {
            $email = base64_decode($encodedEmail);

            ClientUser::where('email_address',$email)->update(['status' => 2]);
    
            $notification = "User has been disabled";    
        } catch(\Exception $e) {
            $notification = "Failed to disable user";
        }

        return back()->with('notification', $notification);
    }
    public function enable_user($encodedEmail)
    {
        try {
            $email = base64_decode($encodedEmail);

            ClientUser::where('email_address',$email)->update(['status' => 1]);
            
            $notification = "User has been enabled";
        } catch(\Exception $e) {
            $notification= "Failed to enable user";
        }
       
        return back()->with('notification', $notification);
    }

    public function create_group(Request $request) {
        try {
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
        } catch (\Throwable $th) {
            $notification = $th->getMessage();
        }

        return back()->with('notification', $notification);
    }
}