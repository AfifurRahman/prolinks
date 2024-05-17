<?php
namespace App\Helpers;
 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Auth;
use App\Models\User;
use App\Models\AdminBackend;

class RolesHelper
{
	public static function get_permission($allow=array())
    {
        $admin = AdminBackend::where('superuser_id', Auth::guard('backend')->user()->superuser_id)->first();
        
        $userRole = [];
        if(!empty($admin->RefRole->access)){
            $userRole = json_decode($admin->RefRole->access, TRUE);
        }

        $result = false;
        foreach ($allow as $key => $value) {
            if (in_array($value, $userRole)) {
                $result = true;
            }
        }

        return $result;
    }

    public static function check_permission($allow_access)
    {
        $permission = \role::get_permission($allow_access);
        if ($permission == false) {
            abort(redirect(route('backend.not-found')));
        }
    }

    public static function get_role_client()
    {
        $roles = Auth::user()->RefClientUser;

        $result = [];
        if (count($roles) > 0) {
            foreach ($roles as $key => $value) {
                $result[] = $value->role;
            }
        }

        return $result;
    }
}