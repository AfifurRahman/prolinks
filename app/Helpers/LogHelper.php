<?php
namespace App\Helpers;
 
use App\Models\AssignProject;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Auth;
use App\Models\User;
use App\Models\AdminBackend;
use App\Models\LogActivity;

class LogHelper
{
	public static function create($request, $response, $description)
    {
        $logs = new LogActivity;
        $logs->ip = $_SERVER['REMOTE_ADDR'];
        $logs->url = url()->current();
        $logs->header = json_encode(request()->header());
        $logs->request = !empty($request) ? json_encode($request) : null;
        $logs->response = !empty($response) ? json_encode($response) : null;
        $logs->method = request()->method();
        $logs->http_status = !empty($http_status) ? $http_status : null;
        $logs->agent = request()->header('User-Agent');
        $logs->user_id = !empty(Auth::user()->user_id) ? Auth::user()->user_id : null;
        $logs->client_id = !empty(Auth::user()->client_id) ? Auth::user()->client_id : null;
        $logs->description = !empty($description) ? $description : null;
        $logs->created_at = date('Y-m-d H:i:s');
        $logs->save();
    }

    public static function get_all($request)
    {
        $model = LogActivity::select('*');

        if (!empty($request->input('client_id'))) {
            $model->where('client_id', $request->input('client_id'));
        }

        if (!empty($request->input('user_id'))) {
            $model->where('user_id', $request->input('user_id'));
        }

        if (!empty($request->input('date_created'))) {
            $model->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), $request->input('date_created'));
        }

        if (!empty($request->input('description'))) {
            $model->where('description', 'LIKE', '%'.$request->input('description').'%');
        }

        if (!empty($request->input('sort'))) {
            $model->orderBy('id', $request->input('sort'));
        }else{
            $model->orderBy('id', 'DESC');
        }

        return $model->paginate(50);
    }

    public static function get_latest()
    {
        $model = LogActivity::orderBy('id', 'DESC')->first();
        return $model; 
    }

    public static function push_notification($text, $type, $resultID=null)
    {
        $assign_project = [];
        if (Auth::user()->type == \globals::set_role_administrator()) {
            $assign_project = AssignProject::where('client_id', Auth::user()->client_id)->where('deleted', 0)->get();
        }else{
            $assign_project = AssignProject::where('subproject_id', Auth::user()->session_project)->where('client_id', Auth::user()->client_id)->where('deleted', 0)->get();
        }
        
        $link = "";
        if ($type == 0) {
            if ($resultID != null) {
                $link = route('discussion.detail-discussion', $resultID);
            }
        }
        
        if (count($assign_project) > 0) {
            foreach ($assign_project as $key => $value) {
                $notif = new Notification;
                $notif->client_id = $value->client_id;
                $notif->user_id = $value->user_id;
                $notif->project_id = $value->project_id;
                $notif->clientuser_id = $value->clientuser_id;
                $notif->subproject_id = $value->subproject_id;
                $notif->type = $type;
                $notif->text = $text;
                $notif->link = $link;
                $notif->sender_name = Auth::user()->name;
                $notif->created_by = Auth::user()->id;
                $notif->created_at = date('Y-m-d H:i:s');
                $notif->save();
            }
        }
    }

    public static function read_notification($id)
    {
        Notification::where('id', $id)->update([
            'is_read' => 1
        ]);
    }

    public static function get_notification($limit=null, $isRead=false)
    {
        $model = [];
        if (Auth::user()->type == \globals::set_role_administrator()) {
            $model = Notification::where('client_id', Auth::user()->client_id)->where('user_id', Auth::user()->user_id);
        }else{
            $model = Notification::where('client_id', Auth::user()->client_id)->where('user_id', Auth::user()->user_id)->where('subproject_id', Auth::user()->session_project);
        }

        if ($isRead == true) {
            $model->where('is_read', 1);
        }else{
            $model->where('is_read', 0);
        }

        if(!empty($limit)){
            $model->limit($limit);
        }

        return $model->orderBy('id', 'DESC')->paginate(30);
    }

    public static function get_all_notification($limit=null)
    {
        $model = [];
        if (Auth::user()->type == \globals::set_role_administrator()) {
            $model = Notification::where('client_id', Auth::user()->client_id)->where('user_id', Auth::user()->user_id);
        }else{
            $model = Notification::where('client_id', Auth::user()->client_id)->where('user_id', Auth::user()->user_id)->where('subproject_id', Auth::user()->session_project);
        }
        
        if(!empty($limit)){
            $model->limit($limit);
        }

        return $model->orderBy('id', 'DESC')->paginate(30);
    }
}