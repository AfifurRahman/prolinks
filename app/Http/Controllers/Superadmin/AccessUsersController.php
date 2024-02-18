<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\AdminBackend;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccessUsersController extends Controller
{
    public function admin_management(Request $request)
    {
        $titles = "ADMIN MANAGEMENT";

        /* set permission */
        $permission = \role::get_permission(array('list-users'));
        if ($permission == false) {
            return redirect(route('backend.not-found'));
        }

        $role = Role::get();
        $admin = AdminBackend::get();

    	return view('superadmin.access_users.admin_management', compact('titles', 'role', 'admin'));
    }

    public function add_admin($id=NULL)
    {
        $titles = "ADD ADMIN MANAGEMENT";

        /* set permission */
        $permission = \role::get_permission(array('add-users'));
        if ($permission == false) {
            return redirect(route('backend.not-found'));
        }

        $role = Role::get();
        $admin = [];
        if ($id != NULL) {
            $titles = "EDIT ADMIN MANAGEMENT";
            $admin = AdminBackend::where('superuser_id', $id)->first();
        }

    	return view('superadmin.access_users.create_admin_management', compact('titles' ,'role', 'admin'));
    }

    public function save_admin(Request $request)
    {
        $id = $request->input('id');
        try {
            \DB::beginTransaction();

            if ($id == NULL) {
                $admin = new AdminBackend;
                $admin->superuser_id = Str::uuid(4);
                $admin->first_name = $request->input('first_name');
                $admin->last_name = $request->input('last_name');
                $admin->email = $request->input('email');
                $admin->phone = $request->input('phone');
                $admin->password = Hash::make($request->input('password'));
                $admin->role = $request->input('role');
                if ($admin->save()) {
                    Alert::success("Success", "Admin user created !");
                }
            }else{
                $param['first_name'] = $request->input('first_name');
                $param['last_name'] = $request->input('last_name');
                $param['email'] = $request->input('email');
                $param['phone'] = $request->input('phone');
                $param['role'] = $request->input('role');
                if (!empty($request->input('password'))) {
                    $param['password'] = Hash::make($request->input('password'));
                }
                
                $update = AdminBackend::where('id', $id)->update($param);
                if ($update) {
                    Alert::success("Success", "Admin user updated !");
                }
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            Alert::error('Error', $e->getMessage());
            return back();
        }

        return redirect(route('backend.access-users.admin-management'));
    }

    public function role(Request $request)
    {
        $titles = "ROLE";

        /* set permission */
        $permission = \role::get_permission(array('list-role'));
        if ($permission == false) {
            return redirect(route('backend.not-found'));
        }

        $role = Role::get();
    	return view('superadmin.access_users.role', compact('titles','role'));
    }

    public function add_role($id=NULL)
    {
        $titles = "ADD ROLE";

        /* set permission */
        $permission = \role::get_permission(array('add-role'));
        if ($permission == false) {
            return redirect(route('backend.not-found'));
        }

        $role = [];
        if ($id != NULL) {
            $titles = "EDIT ROLE";
            $role = Role::where('id', $id)->first();
        }

    	return view('superadmin.access_users.create_role', compact('titles', 'role'));
    }

    public function save_role(Request $request)
    {
        $id = $request->input('id');
        $access = $request->input('menu_access');
        try {
            \DB::beginTransaction();

            $res = array();
            if (count($access) > 0) {
                foreach ($access as $key => $value) {
                    $res[] = $value;
                }
            }

            if ($id == NULL) {
                $role = new Role;
                $role->role_name = $request->input('role_name');
                $role->access = json_encode($res);
                if ($role->save()) {
                    Alert::success("Success", "role created !");
                }
            }else{
                $update = Role::where('id', $id)->update([
                    'role_name' => $request->input('role_name'),
                    'access' => json_encode($res)
                ]);

                if ($update) {
                    Alert::success("Success", "role updated !");
                }
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            Alert::error('Error', $e->getMessage());
            return back();
        }

        return redirect(route('backend.access-users.role'));
    }

    public function delete_role($id)
    {
        $delete = Role::where('id', $id)->delete();
        if ($delete) {
            Alert::success("Success", "role deleted !");
        }

        return back();
    }
}
