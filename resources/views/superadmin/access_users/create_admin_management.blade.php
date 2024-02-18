@extends('layouts.app_backend')

@section('content')
    <style type="text/css">
        .button-submit {
            position: fixed; 
            bottom: 0; 
            right: 0; 
            background-color: #FFFFFF; 
            box-shadow: 0px 0px 1px rgba(0, 0, 0, 0.32), 0px 4px 18px rgba(0, 0, 0, 0.12); 
            width: 100%; 
            height:auto; 
            padding: 15px;
            padding-right: 50px;
        }

        .button-submit button {
            margin-right: 5px;
        }

        .button-submit a {
            margin-right: 5px;
        }

        .box-image {
            border: dashed 1px #CCCCCC;
            width: auto;
            height : 300px;
            padding: 20px;
            border-radius: 10px;
        }
    </style>

    <div class="card-box">
        <form action="{{ route('backend.access-users.save-admin') }}" method="POST" class="form-horizontal" role="form">
            @csrf
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="col-md-3 control-label" style="text-align: left;">First Name<span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="hidden" name="id" value="{{ !empty($admin->id) ? $admin->id : NULL }}">
                            <input required type="text" name="first_name" id="first_name" value="{{ !empty($admin->first_name) ? $admin->first_name : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" style="text-align: left;">Last Name<span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input required type="text" name="last_name" id="last_name" value="{{ !empty($admin->last_name) ? $admin->last_name : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" style="text-align: left;">Phone<span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input required type="text" name="phone" id="phone" value="{{ !empty($admin->phone) ? $admin->phone : '' }}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="col-md-3 control-label" style="text-align: left;">Email<span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input required type="email" name="email" id="email" value="{{ !empty($admin->email) ? $admin->email : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" style="text-align: left;">Password<span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input {{ !empty($admin->id) ? "":"required" }} type="password" name="password" id="password" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" style="text-align: left;">Role<span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select required name="role" id="role" class="form-control">
                                <option value="">-- select role --</option>
                                @if(count($role) > 0)
                                    @foreach($role as $values)
                                        <option value="{{ $values->id }}" {{ !empty($admin->role) && $admin->role == $values->id ? "selected":"" }} >{{ $values->role_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-11">
                    <div class="pull-right">
                        <a href="{{ route('backend.access-users.admin-management') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check"></i> Submit
                        </button>
                    </div>
                </div><div style="clear: both;"></div>
            </div>
        </form>
    </div>
@stop