<?php
namespace App\Helpers;
 
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
}