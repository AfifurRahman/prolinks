<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Company;
use App\Models\Client;
use App\Models\ClientUser;
use Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
		$company = $this->get_company(NULL, $request);

        return view('adminuser.company.index', compact('company'));
    }

    public function detail_company(Request $request, $id)
    {
		$company = $this->get_company($id, $request);
        $clientuser = ClientUser::where('group_id', $company->company_id)->orderBy('group_id', 'ASC')->get();

        return view('adminuser.company.detail_company', compact('company', 'clientuser'));
    }

    public function save(Request $request)
    {
        try {
		  	\DB::beginTransaction();

		  	$id = $request->input('id');

		  	if ($id != NULL) {
		  		$update = Company::where('id', $id)->update([
		  			'company_name' => $request->input('company_name'),
		  			'company_phone' => $request->input('company_phone'),
		  			'company_website' => $request->input('company_website'),
		  			'company_address' => $request->input('company_address'),
		  			'company_city' => $request->input('company_city'),
		  			'company_province' => $request->input('company_province'),
		  			'company_country' => $request->input('company_country'),
		  			'updated_by' => Auth::user()->id,
		  			'updated_at' => date("Y-m-d H:i:s")
		  		]);

		  		if ($update) {
		  			$notification = "Companies updated!";
			  	}
		  	}else{
		  		$companies = new Company;
		  		$companies->company_id = Str::uuid(4);
		  		$companies->user_id = Auth::user()->user_id;
		  		$companies->company_name = $request->input('company_name');
	  			$companies->company_phone = $request->input('company_phone');
	  			$companies->company_website = $request->input('company_website');
	  			$companies->company_address = $request->input('company_address');
	  			$companies->company_city = $request->input('company_city');
	  			$companies->company_province = $request->input('company_province');
	  			$companies->company_country = $request->input('company_country');
	  			$companies->company_status = \globals::set_status_company_active();
	  			$companies->created_by = Auth::user()->id;
	  			$companies->created_at = date("Y-m-d H:i:s");

		  		if ($companies->save()) {
		  			$notification = "Companies created!";
			  	}
		  	}

		  	\DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();
			Alert::error('Error', $e->getMessage());
			return back();
		}

    	return back()->with('notification', $notification);
    }

    public function delete($id)
    {
    	try {
    		\DB::beginTransaction();

    		$deleted = Company::where('company_id', $id)->delete();

    		if ($deleted) {
		  		$notification = "Companies deleted!";
		  	}

    		\DB::commit();
    	} catch (\Exception $e) {
    		\DB::rollback();
			Alert::error('Error', $e->getMessage());
    	}

    	return back()->with('notification', $notification);
    }

    public function disable_company($id)
    {
    	try {
    		\DB::beginTransaction();

    		$updated = Company::where('company_id', $id)->update([
    			'company_status' => \globals::set_status_company_disabled()
    		]);

    		if ($updated) {
		  		$notification = "Companies disabled!";
		  	}

    		\DB::commit();
    	} catch (\Exception $e) {
    		\DB::rollback();
			Alert::error('Error', $e->getMessage());
    	}

    	return back()->with('notification', $notification);
    }

    private function get_company($company_id=NULL, $request)
    {
    	$model = Company::select('*');

    	if (!empty($request->input('company_id'))) {
            $model->where('company_id', $request->input('company_id'));
        }

        if (!empty($request->input('user_id'))) {
            $model->where('user_id', $request->input('user_id'));
        }

        if (!empty($request->input('company_name'))) {
            $model->where('company_name', $request->input('company_name'));
        }

        if (!empty($request->input('created_by'))) {
            $model->where('created_by', $request->input('created_by'));
        }

        if ($company_id != NULL) {
        	return $model->where('company_id', $company_id)->first();
        }else{
        	return $model->orderBy('id', 'DESC')->get();
        }
        
    }
}
