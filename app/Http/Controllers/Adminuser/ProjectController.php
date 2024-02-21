<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Project;
use Auth;

class ProjectController extends Controller
{
    public function create_project()
    {
        return view('adminuser.project.create_project');
    }

    public function save(Request $request)
    {
    	try {
    		\DB::beginTransaction();
    		
    		\DB::commit();
    	} catch (\Exception $e) {
    		\DB::rollback();
			Alert::error('Error', $e->getMessage());
    	}

    	return back();
    }
}
