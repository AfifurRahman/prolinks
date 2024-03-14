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
    <style type="text/css">
        .borderless td, .borderless th {
            border: none !important;
        }

        .not-set{
            color: #CCC;
            font-style: italic;
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
            <h2 id="title" style="color:black;font-size:28px;">{{ !empty($group->group_name) ? $group->group_name : $group->group_name }}</h2>
        </div>
        <div class="pull-right">
            <div class="dropdown" style="margin-top:10px; z-index:99;">
                <button class="btn btn-md dropdown-toggle" type="button" data-toggle="dropdown" style="color:#1570EF; font-weight:bold; background:transparent; border:solid 1px #EDF0F2; border-radius:8px;">
                    Actions&nbsp; <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="">Disable access</a></li>
                    <li><a href="">Delete group</a></li>
                </ul>
            </div>
        </div> <div style="clear:both;"></div>
    </div>
    <div class="company-detail">
        <form action="{{ route('adminuser.access-users.edit-group', $group->group_id) }}" method="POST">
            @csrf
            <table id="formEditGroup" style="display:none;" class="table borderless">
                <tr>
                    <td colspan="2"><h3>Group Information </h3></td>
                </tr>
                <tr>
                    <td width="150">Status</td>
                    <td width="500">
                        {!! \globals::label_status($group->group_status) !!}
                    </td>
                </tr>
                <tr>
                    <td>Group Description</td>
                    <td>
                        <textarea name="group_desc" class="form-control" rows="4">{{ !empty($group->group_desc) ? $group->group_desc : '' }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="button" onclick="closeEditGroup()" class="btn btn-default">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save changes
                        </button>
                    </td>
                </tr>
            </table>
        </form>
        <table id="listGroup" class="table borderless">
            <tr>
                <td colspan="2"><h3>Group Information <img src="{{ url('template/images/edit.png') }}" width="22" height="22" style="cursor:pointer;" onclick="editGroup()" /></h3> </td>
            </tr>
            <tr>
                <td width="150">Status</td>
                <td>
                    {!! \globals::label_status($group->group_status) !!}
                </td>
            </tr>
            <tr>
                <td>Group Description</td>
                <td>
                    <div style="width:500px;">
                        {!! !empty($group->group_desc) ? $group->group_desc : '<span class="not-set">not set</span>' !!}
                    </div>
                </td>
            </tr>
        </table>
        <h3>Member </h3>
        <table class="table table-hover" id="tableMember">
            <thead>
                <tr style="background-color:#F9FAFB;">
                    <th id="name">Name</th>
                    <th id="role">Role</th>
                    <th id="status">&nbsp;Status</th>
                    <th id="lastsigned">Last signed in</th>
                    <th id="navigationdot">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach($member as $user)
                    <tr>
                       <td>
                            <image id="usericon" src="{{ url('template/images/icon_access_users.png') }}" width="24" height="24">
                            {{ $user->RefClientUser->email_address }}
                        </td>
                        <td>
                            @if($user->RefClientUser->role == 0) 
                                Administrator
                            @elseif($user->RefClientUser->role == 1)
                                Collaborator
                                @elseif($user->RefClientUser->role == 2)
                                Client
                            @endif
                        </td>
                        <td>
                            @if($user->RefClientUser->email_address == Auth::User()->email)
                                <span class="active_status">You</span>
                            @elseif($user->RefClientUser->status == 1)
                                <span class="active_status">Active</span>
                            @elseif($user->RefClientUser->status == 2)
                                <span class="disabled_status">Disabled</span>
                            @elseif($user->RefClientUser->status == 0)
                                <span class="invited_status">Invited</span>
                            @endif
                        </td>
                        <td>
                            @if(is_null(App\Models\User::where('email', $user->RefClientUser->email_address)->value('last_signed')))
                                -
                            @else
                                {{ date('d M Y, H:i', strtotime(App\Models\User::where('email', $user->RefClientUser->email_address)->value('last_signed'))) }}
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="button_ico dropdown-toggle" data-toggle="dropdown" style="background: transparent; border:none; ">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-top pull-right">
                                    <li><a onclick="moveGroup('{{ base64_encode($user->RefClientUser->email_address) }}')">Move to group</a></li>
                                    @if($user->RefClientUser->status == 1)
                                        <li><a href="{{ route('adminuser.access-users.disable-user', base64_encode($user->RefClientUser->email_address)) }}">Disable User</a></li>
                                    @elseif($user->RefClientUser->status == 2)
                                        <li><a href="{{ route('adminuser.access-users.enable-user', base64_encode($user->RefClientUser->email_address)) }}">Enable User</a></li>
                                    @endif
                                    <li><a href="{{ route('adminuser.access-users.resend-email', base64_encode($user->RefClientUser->email_address)) }}"></i>Send Email</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop

@push('scripts')
    <script>
        function editGroup() {
            $("#formEditGroup").css("display", "block");
            $("#listGroup").css("display", "none");
        }

        function closeEditGroup() {
            $("#formEditGroup").css("display", "none");
            $("#listGroup").css("display", "block");
        }

        function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();
    </script>
@endpush