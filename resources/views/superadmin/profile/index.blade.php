@extends('layouts.app_backend')

@section('content')
	<div class="row">
	    <div class="col-xs-6">
	    	<div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title text-info">
                        User Profile
                    </h3>
                </div>
                <div id="bg-default">
                    <div class="panel-body">
                        <form action="{{ route('backend.save-profile') }}" method="POST">
                        	@csrf
                        	<div class="form-group">
                        		<label>First Name</label>
                        		<input required type="text" name="first_name" id="first_name" class="form-control" value="{{ !empty($profile->first_name) ? $profile->first_name : '' }}">
                        	</div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input required type="text" name="last_name" id="last_name" class="form-control" value="{{ !empty($profile->last_name) ? $profile->last_name : '' }}">
                            </div>
                        	<div class="form-group">
                        		<label>Email</label>
                        		<input required type="email" name="email" id="email" class="form-control" value="{{ !empty($profile->email) ? $profile->email : '' }}">
                        	</div>
                        	<div class="form-group">
                        		<label>Phone</label>
                        		<input required type="text" name="phone" id="phone" class="form-control" value="{{ !empty($profile->phone) ? $profile->phone : '' }}">
                        	</div>
                        	<div class="form-group">
                        		<button class="btn btn-info col-xs-12">
                        			Submit
                        		</button>
                        	</div>
                        	<div style="clear: both;"></div>
                        </form>
                    </div>
                </div>
            </div>
	    </div>
	    <div class="col-xs-6">
	    	<div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title text-info">
                        Change Password
                    </h3>
                </div>
                <div id="bg-default">
                    <div class="panel-body">
                        <form action="{{ route('backend.change-password') }}" method="POST">
                        	@csrf
                        	<div class="form-group">
                        		<label>New Password</label>
                        		<input required type="password" name="password" id="password" class="form-control">
                        	</div>
                        	<div class="form-group">
                        		<button class="btn btn-info col-xs-12">
                        			Submit
                        		</button>
                        	</div>
                        	<div style="clear: both;"></div>
                        </form>
                    </div>
                </div>
            </div>
	    </div>
	</div>
@endsection