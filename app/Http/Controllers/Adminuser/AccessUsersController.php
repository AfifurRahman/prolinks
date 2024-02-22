<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccessUsersController extends Controller
{
    public function index()
    {
        return view('adminuser.users.index');
    }

    public function invite_user()
    {
        return view('adminuser.users.invite_user');
    }

    public function create_group()
    {
        return view('adminuser.users.create_group');
    }
}
