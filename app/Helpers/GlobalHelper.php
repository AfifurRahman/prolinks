<?php
namespace App\Helpers;
 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Auth;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Project;
use App\Models\Client;

class GlobalHelper
{
	public static function formatBytes($bytes) { 
	    if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
	}

    public static function formatBytes2($bytes) { 
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824);
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576);
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024);
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes;
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes;
        }
        else
        {
            $bytes = 0;
        }

        return $bytes;
    } 
	
	public static function get_breadcumbs_backend()
    {
        $url = URL::current();
        $parsePath = parse_url($url);
        $explode = explode("/", $parsePath['path']);
        
        $result = "";
        if (count($explode) > 0) {
            foreach ($explode as $key => $value) {
                if ($key > 1) {
                    $result .= "<li>";
                    $result .= "<a href=''>".ucfirst(str_replace("-", " ", $value))."</a>";
                    $result .= "</li>";
                }
                
            }
        }
        
        return $result;
    }

    public static function set_status_active()
    {
        return 1;
    }

    public static function set_status_nonactive()
    {
        return 2;
    }

    public static function label_status($status)
    {
        $results = "";
        if($status == \globals::set_status_active()){
            $results = '<label class="label label-success" style="border-radius:10px;"><i class="fa fa-check"></i> ACTIVE</label>';
        }elseif($status == \globals::set_status_nonactive()){
            $results = '<label class="label label-danger" style="border-radius:10px;"><i class="fa fa-remove"></i> NON ACTIVE</label>';
        }

        return $results;
    }

    public static function set_usertype_admin()
    {
        return 1;
    }

    public static function set_usertype_client()
    {
        return 2;
    }

    public static function get_usertype()
    {
        return array(
            array(
                'id' => \globals::set_usertype_admin(),
                'name' => "Admin User"
            ),
            array(
                'id' => \globals::set_usertype_client(),
                'name' => "Client"
            ),
        );
    }

    public static function set_type_pricing_allocation_only()
    {
        return 1;
    }

    public static function set_type_pricing_allocation_date()
    {
        return 2;
    }

    public static function get_type_pricing()
    {
        return array(
            array(
                'id' => \globals::set_type_pricing_allocation_only(),
                'name' => "Allocation Only"
            ),
            array(
                'id' => \globals::set_type_pricing_allocation_date(),
                'name' => "Allocation & Duration"
            ),
        );
    }

    public static function label_type_pricing($type)
    {
        $results = "";
        if($type == \globals::set_type_pricing_allocation_only()){
            $results = '<label class="label label-inverse" style="border-radius:10px;"> Allocation Only</label>';
        }elseif($type == \globals::set_type_pricing_allocation_date()){
            $results = '<label class="label label-inverse" style="border-radius:10px;"> Allocation & Duration</label>';
        }

        return $results;
    }

    public static function create_pswd_client_yes()
    {
        return 1;
    }

    public static function create_pswd_client_no()
    {
        return 2;
    }

    public static function get_project_sidebar()
    {
        $models = Project::where('user_id', Auth::user()->user_id)->orderBy('id', 'DESC')->get();
        return $models;
    }

    public static function set_status_company_active()
    {
        return 1;
    }

    public static function set_status_company_disabled()
    {
        return 2;
    }

    public static function label_status_company($status)
    {
        $results = "";
        if($status == \globals::set_status_company_active()){
            $results = '<span class="active_status"> Active</span>';
        }elseif($status == \globals::set_status_company_disabled()){
            $results = '<span class="disabled_status"> Disabled</span>';
        }

        return $results;
    }

    public static function get_client_id()
    {
        return Auth::user()->RefClient->client_id;
    }

    public static function get_project_id()
    {
        return Auth::user()->session_project;
    }

    public static function set_status_discussion_open()
    {
        return 0;
    }

    public static function set_status_discussion_submitted()
    {
        return 1;
    }

    public static function set_status_discussion_replied()
    {
        return 2;
    }

    public static function set_status_discussion_unanswered()
    {
        return 3;
    }

    public static function set_status_discussion_closed()
    {
        return 4;
    }

    public static function set_status_discussion_high()
    {
        return 5;
    }

    public static function label_status_discussion($status)
    {
        $results = "";
        if($status == \globals::set_status_discussion_open()){
            $results = '<label class="label label-primary" style="border-radius:8px;"> Open</label>';
        }elseif($status == \globals::set_status_discussion_submitted()){
            $results = '<label class="label label-success" style="border-radius:8px;"> Submitted</label>';
        }elseif($status == \globals::set_status_discussion_replied()){
            $results = '<label class="label label-info" style="border-radius:8px;"> Replied</label>';
        }elseif($status == \globals::set_status_discussion_unanswered()){
            $results = '<label class="label label-warning" style="border-radius:8px;"> Unanswered</label>';
        }elseif($status == \globals::set_status_discussion_closed()){
            $results = '<label class="label label-inverse" style="border-radius:8px;"> Closed</label>';
        }elseif($status == \globals::set_status_discussion_high()){
            $results = '<label class="label label-danger" style="border-radius:8px;"> High</label>';
        }

        return $results;
    }
}