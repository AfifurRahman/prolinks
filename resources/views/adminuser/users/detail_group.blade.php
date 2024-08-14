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
    <link href="{{ url('clientuser/userindex.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .borderless td, .borderless th {
            border: none !important;
        }

        .not-set{
            color: #CCC;
            font-style: italic;
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
                    @if($group->group_status == 1)
                        <li><a href="#modal-disabled-group" data-toggle="modal" data-url="{{ route('adminuser.access-users.disabled-group', $group->group_id) }}" onclick="getUrlDisableGroup(this)">Disable access</a></li>
                    @elseif($group->group_status == 2)
                        <li><a href="#modal-enable-group" data-toggle="modal" data-url="{{ route('adminuser.access-users.enable-group', $group->group_id) }}" onclick="getUrlEnableGroup(this)">Enable access</a></li>
                    @endif
                    <li><a href="#modal-delete-group" data-toggle="modal" data-url="{{ route('adminuser.access-users.delete-group', $group->group_id) }}" onclick="getUrlDeleteGroup(this)" style="color:#D92D20;">Delete Group</a></li>
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
                        @if($group->group_status == 1)
                            <span class="active_status"> Active</span>
                        @elseif($group->group_status == 2)
                            <span class="disabled_status"> Disabled</span>
                        @endif
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
                    @if($group->group_status == 1)
                        <span class="active_status"> Active</span>
                    @elseif($group->group_status == 2)
                        <span class="disabled_status"> Disabled</span>
                    @endif
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
        @if(count($member) > 0)
            <table id="tableUser">
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
                                @if(!empty($user->RefUser->name) && $user->RefUser->name != "null")
                                    {!! \globals::get_user_avatar_small($user->RefUser->user_id, !empty($user->RefUser->avatar_color) ? $user->RefUser->avatar_color : '#000') !!}
                                    {{ !empty($user->RefClientUser->name) ? $user->RefClientUser->name : $user->RefClientUser->email_address }}
                                @else
                                    {!! \globals::get_user_avatar_small($user->RefClientUser->email_address, !empty($user->RefUser->avatar_color) ? $user->RefUser->avatar_color : '#000') !!}
                                    {{ $user->RefClientUser->email_address }}
                                @endif
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
                                    <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-top pull-right">
                                        @if($user->RefClientUser->role == \globals::set_role_client())
                                            <li><a href="#modal-move-group" data-toggle="modal" onclick="moveGroup('{{ base64_encode($user->RefClientUser->email_address) }}')">Move to group</a></li>
                                        @endif

                                        @if($user->RefClientUser->status == 1)
                                            <li><a href="{{ route('adminuser.access-users.disable-user', base64_encode($user->RefClientUser->email_address)) }}">Disable User</a></li>
                                        @elseif($user->RefClientUser->status == 2)
                                            <li><a href="{{ route('adminuser.access-users.enable-user', base64_encode($user->RefClientUser->email_address)) }}">Enable User</a></li>
                                        @endif

                                        @if($user->RefClientUser->status == 0)
                                            <li><a href="{{ route('adminuser.access-users.resend-email', base64_encode($user->RefClientUser->email_address)) }}"></i>Resend invitation email</a></li>
                                        @endif
                                        <li><a onclick="return confirm('are you sure delete this user ?')" href="{{ route('adminuser.access-users.delete-user', base64_encode($user->RefClientUser->email_address)) }}" style="color:#D92D20;">Delete User</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="card-box">
                <center>
                    <img src="{{ url('template/images/empty_qna.png') }}" width="300" />
                </center>    
            </div>
        @endif
    </div>

    @include('adminuser.users.modal_disable_group')
    @include('adminuser.users.modal_enable_group')
    @include('adminuser.users.modal_delete_group')
@stop

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#tableUser').dataTable({
                "bPaginate": true,
                "bInfo": false,
                "bSort": false,
                "dom": 'rtip',
                "stripeClasses": false,
                "pageLength": 8,
            });
        });

        function getUrlDisableGroup(element) {
            var url = $(element).data('url');
            $("#get_url_disable_group").val(url);
        }

        function getUrlEnableGroup(element) {
            var url = $(element).data('url');
            $("#get_url_enable_group").val(url);
        }

        function actDisableGroup() {
            var getUrlDisabled = $("#get_url_disable_group").val();
            if (getUrlDisabled != 'undefined') {
                window.location.href = getUrlDisabled;
            }
        }

        function actEnableGroup() {
            var getUrlEnable = $("#get_url_enable_group").val();
            if (getUrlEnable != 'undefined') {
                window.location.href = getUrlEnable;
            }
        }

        function getUrlDeleteGroup(element) {
            var url = $(element).data('url');
            $("#get_url_delete_group").val(url);
        }

        function actDeleteGroup() {
            var getUrlDelete = $("#get_url_delete_group").val();
            if (getUrlDelete != 'undefined') {
                window.location.href = getUrlDelete;
            }
        }
        
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