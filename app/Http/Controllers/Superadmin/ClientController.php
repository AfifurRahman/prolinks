<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Client;
use App\Models\Pricing;
use Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class ClientController extends Controller
{
    public function index(Request $request)
    {
    	\role::check_permission(array('list-client'));

    	$titles = "CLIENTS";
    	$clients = $this->get_clients(NULL, $request);
    	
        return view('superadmin.clients.index', compact('clients', 'titles'));
    }

    public function add($id=NULL)
    {
        \role::check_permission(array('add-client'));
      	
    	$titles = "ADD CLIENTS";
    	$clients = [];
    	if ($id != NULL) {
    		$titles = "EDIT CLIENTS";
    		$clients = Client::where('client_id', $id)->first();
    	}
    	
    	$pricing = Pricing::where('pricing_status', \globals::set_status_active())->get();
        return view('superadmin.clients.add_clients', compact('clients', 'pricing', 'titles'));
    }

    public function save(Request $request)
    {
    	\role::check_permission(array('add-client'));

    	try {
		  	\DB::beginTransaction();

		  	$id = $request->input('id');

		  	if ($id != NULL) {
		  		$update = Client::where('id', $id)->update([
		  			'client_name' => $request->input('client_name'),
		  			'client_email' => $request->input('client_email'),
		  			'client_phone' => $request->input('client_phone'),
		  			'client_website' => $request->input('client_website'),
		  			'client_vat' => $request->input('client_vat'),
		  			'client_address' => $request->input('client_address'),
		  			'client_city' => $request->input('client_city'),
		  			'client_state' => $request->input('client_state'),
		  			'client_country' => $request->input('client_country'),
		  			'pricing_id' => $request->input('pricing_id'),
		  			'updated_by' => Auth::guard('backend')->user()->id,
	  				'updated_at' => date("Y-m-d H:i:s"),
		  		]);

		  		if ($update) {
			  		Alert::success('Success', 'Client updated !');
			  	}else{
			  		Alert::error('Error', 'Failed');
			  	}
		  	}else{
		  		$clients = new Client;
		  		$clients->client_id = Str::uuid(4);
		  		$clients->user_id = 0;
		  		$clients->client_name = $request->input('client_name');
		  		$clients->client_email = $request->input('client_email');
	  			$clients->client_phone = $request->input('client_phone');
	  			$clients->client_website = $request->input('client_website');
	  			$clients->client_vat = $request->input('client_vat');
	  			$clients->client_address = $request->input('client_address');
	  			$clients->client_city = $request->input('client_city');
	  			$clients->client_state = $request->input('client_state');
	  			$clients->client_country = $request->input('client_country');
	  			$clients->client_status = 1;
	  			$clients->pricing_id = $request->input('pricing_id');
	  			$clients->created_by = Auth::guard('backend')->user()->id;
	  			$clients->created_at = date("Y-m-d H:i:s");

		  		if ($clients->save()) {

		  			$users = new User;
		  			$users->user_id = Str::uuid(4);
		  			$users->name = $clients->client_name;
		  			$users->email = $clients->client_email;
		  			$users->type = 0;
		  			$users->password = Hash::make(bcrypt(Str::random(255)));
		  			
		  			if ($users->save()) {
		  				Client::where('id', $clients->id)->update([
		  					'user_id' => $users->user_id
		  				]);

		  				//send mail
		  				$token = Password::getRepository()->create($users);
		  				$details = [
		  					'client_name' => $clients->client_name,
		  					'link' => URL::to('/create-password') . '/' . $token . '?email=' . str_replace("@", "%40", $clients->client_email),
		  				];
		  				$sendMail = \Mail::to($users->email)->send(new \App\Mail\CreateAdminUsersPassword($details));
		  				if ($sendMail) {
		  					/* update token */
			    			User::where('id', $users->id)->update([
			    				'remember_token' => $token
			    			]);
		  				}
		  			}

			  		Alert::success('Success', 'Client added !');
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

    	return redirect(route('backend.client.list'));
    }

    public function delete($id)
    {
    	\role::check_permission(array('delete-client'));

    	try {
    		\DB::beginTransaction();

    		$delete = Client::where('client_id', $id)->delete();

    		if ($delete) {
		  		Alert::success('Success', 'Client deleted !');
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

    public function send_email($id)
    {
    	\role::check_permission(array('send-email-client'));

    	try {
    		$users = User::select('users.id','users.email', 'clients.client_name')->join('clients', 'users.id', 'clients.user_id')->where('clients.client_id', $id)->first();
    		//send mail
			$token = Password::getRepository()->create($users);
			$details = [
				'client_name' => $users->client_name,
				'link' => URL::to('/create-password') . '/' . $token . '?email=' . str_replace("@", "%40", $users->email),
			];
			$sendMail = \Mail::to($users->email)->send(new \App\Mail\CreateAdminUsersPassword($details));
    		if ($sendMail) {
    			/* update token */
    			User::where('id', $users->id)->update([
    				'remember_token' => $token
    			]);
    			Alert::success('Success', 'Send email to client success !');
    		}
    		
    	} catch (\Exception $e) {
    		Alert::error('Error', $e->getMessage());
    	}

    	return back();
    }

    private function get_clients($client_id=NULL, $request)
    {
    	$model = Client::select('*');

    	if (!empty($request->input('client_name'))) {
            $model->where('client_name', $request->input('client_name'));
        }

        if (!empty($request->input('client_name'))) {
            $model->where('client_name', $request->input('client_name'));
        }

        if (!empty($request->input('client_phone'))) {
            $model->where('client_phone', $request->input('client_phone'));
        }

        if (!empty($request->input('status'))) {
            $model->where('status', $request->input('status'));
        }

        if (!empty($request->input('created_by'))) {
            $model->where('created_by', $request->input('created_by'));
        }

        if ($client_id != NULL) {
        	return $model->where('client_id', $client_id)->first();
        }else{
        	return $model->orderBy('id', 'DESC')->get();
        }
        
    }
}
