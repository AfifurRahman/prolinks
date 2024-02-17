<?php
namespace App\Helpers;
 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Auth;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;

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

    public static function formatBytes2($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
       
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
       
        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow)); 
       
        return round($bytes, $precision) . $units[$pow]; 
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
            $results = '<label class="label label-success"><i class="fa fa-check"></i> ACTIVE</label>';
        }elseif($status == \globals::set_status_nonactive()){
            $results = '<label class="label label-danger"><i class="fa fa-remove"></i> NON ACTIVE</label>';
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
            $results = '<label class="label label-inverse"> Allocation Only</label>';
        }elseif($type == \globals::set_type_pricing_allocation_date()){
            $results = '<label class="label label-inverse"> Allocation & Duration</label>';
        }

        return $results;
    }
}