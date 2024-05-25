<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use App\Models\SettingEmailNotification;
use Illuminate\Http\Request;
use Session;
use Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SettingController extends Controller
{
    public function index()
	{
        $setting = "";
        if (Auth::user()->type == \globals::set_role_administrator()) {
            $setting = SettingEmailNotification::where('client_id', \globals::get_client_id())->where('user_id', Auth::user()->user_id)->first();
        }else{
            $setting = SettingEmailNotification::where('client_id', \globals::get_client_id())->where('user_id', Auth::user()->user_id)->where('subproject_id', Auth::user()->session_project)->first();
        }
		
		return view('adminuser.setting.index', compact('setting'));
	}

    public function save_setting_email(Request $request) {
        $notification = "";
        try {
            \DB::beginTransaction();

            if (Auth::user()->type == \globals::set_role_administrator()) {
                SettingEmailNotification::where('client_id', \globals::get_client_id())->where('user_id', Auth::user()->user_id)->update([
                    'is_upload_file' => !empty($request->is_upload_file) ? 1 : 0,
                    'is_discussion' => !empty($request->is_discussion) ? 1 : 0,
                    'is_change_role' => !empty($request->is_change_role) ? 1 : 0
                ]);
            }else{
                SettingEmailNotification::where('client_id', \globals::get_client_id())->where('user_id', Auth::user()->user_id)->where('subproject_id', Auth::user()->session_project)->update([
                    'is_upload_file' => !empty($request->is_upload_file) ? 1 : 0,
                    'is_discussion' => !empty($request->is_discussion) ? 1 : 0,
                    'is_change_role' => !empty($request->is_change_role) ? 1 : 0
                ]);
            }

            $desc = Auth::user()->name." changed setting email notification";
            \log::create(request()->all(), "success", $desc);
            $notification = "Setting email updated";

            \DB::commit();
        } catch (\Exception $e) {
            $notification = "Failed update setting email";
            \DB::rollback();

            \log::create($request->all(), "error", $e->getMessage());
			Alert::error('Error', $e->getMessage());
			return back();
        }

        return redirect(route('setting', 'tab=email_setting'))->with('notification', $notification);
    }

    public function all_notification() {
        $notification = \log::get_all_notification();
        return view('adminuser.setting.notification', compact('notification'));
    }

    function read_notification(Request $request){
        $id = $request->input('id');
        \log::read_notification($id);
    }
}