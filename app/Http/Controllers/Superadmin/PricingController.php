<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Pricing;
use Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

class PricingController extends Controller
{
    public function index(Request $request)
    {
    	\role::check_permission(array('list-pricing'));
        
    	$titles = "PRICING";
    	$pricing = $this->get_pricing(NULL, $request);
    	
        return view('superadmin.pricing.index', compact('pricing', 'titles'));
    }

    public function save(Request $request)
    {
        \role::check_permission(array('add-pricing'));
        
    	try {
		  	\DB::beginTransaction();

		  	$id = $request->input('id');

		  	if ($id != NULL) {
		  		$update = Pricing::where('id', $id)->update([
		  			'pricing_name' => $request->input('pricing_name'),
		  			'pricing_desc' => $request->input('pricing_desc'),
		  			'pricing_type' => $request->input('pricing_type'),
		  			'duration' => $request->input('duration'),
		  			'allocation_size' => $request->input('allocation_size'),
		  			'updated_by' => Auth::guard('backend')->user()->id,
		  			'updated_at' => date("Y-m-d H:i:s")
		  		]);

		  		if ($update) {
			  		Alert::success('Success', 'Pricing updated !');
			  	}else{
			  		Alert::error('Error', 'Failed');
			  	}
		  	}else{
		  		$pricing = new Pricing;
		  		$pricing->pricing_id = Str::uuid(4);
		  		$pricing->pricing_name = $request->input('pricing_name');
		  		$pricing->pricing_desc = $request->input('pricing_desc');
	  			$pricing->pricing_type = $request->input('pricing_type');
	  			$pricing->duration = $request->input('duration');
	  			$pricing->pricing_status = 1;
	  			$pricing->created_by = Auth::guard('backend')->user()->id;
	  			$pricing->created_at = date("Y-m-d H:i:s");

                $size_type = $request->input('size_type');
                $convt_allocation = 0;
                if ($size_type == "MB") {
                    $convt_allocation = $request->input('allocation_size') * 1048576;
                }elseif ($size_type == "GB") {
                    $convt_allocation = $request->input('allocation_size') * 1073741824;
                }

                $new_size_type = "";
                if ($size_type >= 1073741824){
                    $new_size_type = "GB";
                }elseif ($size_type >= 1048576) {
                    $new_size_type = "MB";
                }

                $pricing->allocation_size = $convt_allocation;
                $pricing->size_type = $new_size_type;

		  		if ($pricing->save()) {
			  		Alert::success('Success', 'Pricing added !');
			  	}else{
			  		Alert::error('Error', 'Failed');
			  	}
		  	}

		  	\DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();
			Alert::error('Error', $e->getMessage());
			return back();
		}

    	return redirect(route('backend.pricing.list'));
    }

    public function delete($id)
    {
    	\role::check_permission(array('delete-pricing'));
       	
    	try {
    		\DB::beginTransaction();

    		$delete = Pricing::where('pricing_id', $id)->delete();

    		if ($delete) {
		  		Alert::success('Success', 'Pricing deleted !');
		  	}else{
		  		Alert::error('Error', 'Failed');
		  	}

    		\DB::commit();
    	} catch (\Exception $e) {
    		\DB::rollback();
			Alert::error('Error', $e->getMessage());
    	}

    	return back();
    }

    private function get_pricing($pricing_id=NULL, $request)
    {
    	$model = Pricing::select('*');

    	if (!empty($request->input('pricing_name'))) {
            $model->where('pricing_name', $request->input('pricing_name'));
        }

        if (!empty($request->input('pricing_type'))) {
            $model->where('pricing_type', $request->input('pricing_type'));
        }

        if (!empty($request->input('pricing_status'))) {
            $model->where('pricing_status', $request->input('pricing_status'));
        }

        if (!empty($request->input('created_by'))) {
            $model->where('created_by', $request->input('created_by'));
        }

        if ($pricing_id != NULL) {
        	return $model->where('pricing_id', $pricing_id)->first();
        }else{
        	return $model->orderBy('id', 'DESC')->get();
        }
        
    }
}
