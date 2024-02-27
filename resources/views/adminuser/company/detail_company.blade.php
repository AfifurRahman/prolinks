@extends('layouts.app_client')

@section('notification')
    @if(session('notification'))
        <div class="notificationlayer">
            <div class="notification" id="notification">
                <image class="notificationicon" src="{{ url('template/images/icon_menu/checklist.png') }}"></image>
                <p class="notificationtext">{{ session('notification') }}</p>
            </div>
        </div>
    @endif
@endsection

@section('content')
	<script type="text/javascript">
		var title = document.getElementById('title');
		title.textContent = "Detail Companies";
	</script>
    <style type="text/css">
        .borderless td, .borderless th {
            border: none !important;
        }

        .tableUsers td {
            vertical-align: middle;
        }

        .tableUsers{
            border-collapse: separate;
            border:1px solid #F1F1F1;
            border-radius: 7px;
            width:100%
        }

        .tableUsers th {
            padding: 15px 0px 15px 10px;
            border-bottom:1px solid #F1F1F1;
            font-size:14px;
            font-weight:600;
        }

        .tableUsers td  {
            padding: 13px 0px 13px 10px;
            border-bottom:1px solid #F1F1F1;
            font-size:13.5px;
            color:black;
        }

        .tableUsers tbody tr:last-child td{
            border-bottom: none;
        }

        .tableUsers tbody tr:hover {
            background-color: #f0f0f0;
        }

        #usericon {
            margin-top:-4px;
            margin-right:4px;
            width:25px;
            height:25px;
        }

        .invited_status{
            background: #EDF0F2;
            font-size:12px;
            font-weight:600;
            color: #1D2939;
            padding:5px 10px 5px 10px;
            border-radius:25px;
        }

        .active_status{
            background: #ECFDF3;
            font-size:12px;
            font-weight:600;
            color: #027A48; 
            padding:5px 10px 5px 10px;
            border-radius:25px;
        }

        .you_status{
            background: #D1E9FF;
            font-size:12px;
            font-weight:600;
            color: #175CD3; 
            padding:5px 10px 5px 10px;
            border-radius:25px;
        }

        .modal-content {
            -webkit-border-radius: 0px !important;
            -moz-border-radius: 0px !important;
            border-radius: 10px !important; 
        }

        .notificationlayer {
            position: absolute;
            width:100%;
            height:50px;
            z-index: 1;
            pointer-events: none;
        }

        #notification {
            background-color: #FFFFFF;
            border: 2px solid #12B76A;
            border-radius: 8px;
            display: flex;
            color: #232933;
            margin: 50px auto;
            text-align: center;
            height: 48px;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            transition: top 0.5s ease;    
        }

        .notificationicon {
            width:20px;
            height:20px;
            margin-top:11px;
            margin-left:15px;
        }

        .notificationtext{
            margin-top:11px;
            margin-left:8px;
            margin-right:13px;
            font-size:14px;
        }
    </style>
    <div class="header-detail">
        <div class="pull-left">
            <h3>{{ $company->company_name }}</h3>
        </div>
        <div class="pull-right">
            <a href="#modal-add-company" data-toggle="modal" data-title="Edit Company" data-query="{{ $company }}" onclick="getDetailCompanies(this)" class="btn btn-sm btn-default"> <i class="fa fa-edit"></i> Edit</a>
        </div>
        <div class="pull-right">
            <div class="dropdown">
                <button class="btn btn-sm btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" style="border: solid 0px;">More
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('company.disable-company', $company->company_id) }}" onclick="return confirm('are you sure disable this company ?')"><i class="fa fa-unlock-alt"></i> Disable</a></li>
                    <li><a href="{{ route('company.delete-company', $company->company_id) }}" onclick="return confirm('are you sure delete this item ?')"><i class="fa fa-trash"></i> Delete</a></li>
                </ul>
            </div>
        </div>
    </div><div style="clear: both;"></div>
    <div class="company-detail">
        <table class="table table-hover borderless">
            <tr>
                <td width="150">Status</td>
                <td>{!! \globals::label_status_company($company->company_status) !!}</td>
            </tr>
            <tr>
                <td>Phone Number</td>
                <td>{{ $company->company_phone }}</td>
            </tr>
            <tr>
                <td>Website</td>
                <td>{{ $company->company_website }}</td>
            </tr>
            <tr>
                <td>Address</td>
                <td>{{ $company->company_address }}</td>
            </tr>
            <tr>
                <td>City</td>
                <td>{{ $company->company_city }}</td>
            </tr>
            <tr>
                <td>Province</td>
                <td>{{ $company->company_province }}</td>
            </tr>
            <tr>
                <td>Country</td>
                <td>{{ $company->company_country }}</td>
            </tr>
        </table>
        <ul class="nav nav-tabs tabs-bordered">
            <li class="active">
                <a href="#tabs-users" data-toggle="tab" aria-expanded="true">
                    <span class="visible-xs"><i class="fa fa-home"></i></span>
                    <span class="hidden-xs" style="text-transform: capitalize;">Users</span>
                </a>
            </li>
            <li>
                <a href="#tabs-projects" data-toggle="tab" aria-expanded="false">
                    <span class="visible-xs"><i class="fa fa-user"></i></span>
                    <span class="hidden-xs" style="text-transform: capitalize;">Projects</span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tabs-users">
                <table class="tableUsers">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last signed in</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientuser as $user)
                            <tr>
                                <td>
                                    <image id="usericon" src="{{ url('template/images/Avatar.png') }}"></image>
                                    {{ $user->email_address }}
                                </td>
                                <td>
                                    @if($user->role == 0) 
                                        Administrator
                                    @elseif($user->role == 1)
                                        Collaborator
                                    @endif
                                </td>
                                <td>
                                    @if($user->email_address == Auth::User()->email)
                                        <span class="active_status">You</span>
                                    @elseif($user->status == 1)
                                        <span class="active_status">Active</span>
                                    @elseif($user->status == 0)
                                        <span class="invited_status">Invited</span>
                                    @endif
                                </td>
                                <td>
                                    @if(is_null(App\Models\User::where('email', $user->email_address)->first()->last_signed))
                                        -
                                    @else
                                        {{ date('d M Y, H:i', strtotime(App\Models\User::where('email', $user->email_address)->first()->last_signed)) }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane" id="tabs-projects">
            
            </div>
        </div>
    </div>

    @include('adminuser.company._form')
@stop