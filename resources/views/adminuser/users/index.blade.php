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
    <div id="box_helper">
        <h2 id="title" style="color:black;font-size:28px;">Users</h2>
        <div>
            <!-- <button id="create_group" onclick="document.getElementById('create_group_form').style.display='block'"><span style="color:#1570EF; font-weight:bold;">Create Group</span></button> -->
            <!-- <button id="invite_user" onclick="document.getElementById('inviteuser_form').style.display='block'"><image id="addimg" src="{{ url('template/images/icon_menu/add.png') }}"></image>Invite User</button> -->
            <a id="create_group" class="btn" href="#modal-create-group" data-toggle="modal"><span style="color:#1570EF; font-weight:bold;">Create Group</span></a>
            <a id="invite_user" class="btn" href="#modal-invite-users" data-toggle="modal"><img id="addimg" src="{{ url('template/images/icon_menu/add.png') }}" />Invite User</a>
        </div>
    </div>

    <div id="box_helper">
        <div>
            <button id="filter_button">
                <image id="filtericon" src="{{ url('template/images/icon_menu/filter.png') }}"></image>
                Filter
            </button>
        </div>
        <div class="switch-box">
            <div class="switch-user {{ !empty(request()->input('tab')) && request()->input('tab') == "user" ? "active-box":"" }}">
                <a href="?tab=user"><img src="{{ url('template/images/icon_menu/user.png') }}"> User</a>
            </div>
            <div class="switch-group {{ !empty(request()->input('tab')) && request()->input('tab') == "group" ? "active-box":"" }}">
                <a href="?tab=group"><img src="{{ url('template/images/icon_menu/group.png') }}"> Group</a>
            </div>
        </div>
        <div id="searchbox">
            <image id="searchicon" src="{{ url('template/images/icon_menu/search.png') }}"></image>
            <input type="text" id="search_bar" placeholder="Search users...">
        </div>
    </div>
    
    @if(request()->input('tab') == "user")
        <div id="table">
            <table id="tableUser">
                <thead>
                    <tr>
                        <th id="check"><input type="checkbox" id="checkbox" disabled/></th>
                        <th id="name">Name</th>
                        <th id="company">Group</th>
                        <th id="role">Role</th>
                        <th id="status">&nbsp;Status</th>
                        <th id="lastsigned">Last signed in</th>
                        <th id="navigationdot">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($owners) > 0)
                        @foreach($owners as $owner)
                            <tr class="company-group">
                                <td>
                                    <input type="checkbox" id="checkbox" disabled />
                                </td>
                                <td>
                                    {!! \globals::get_user_avatar_small($owner->name, $owner->avatar_color) !!}
                                    <!-- {{ $owner->email }} -->
                                    {{ $owner->name }}
                                </td>
                                <td>
                                    <span class="you_status">All</span>
                                </td> 
                                <td>
                                    Administrator
                                </td>
                                <td>
                                    @if(Auth::user()->email == $owner->email)
                                        <span class="you_status">You</span>
                                    @else
                                        <span class="active_status">Active</span>
                                    @endif
                                </td>
                                <td>
                                    @if(is_null($owner->last_signed))
                                        -
                                    @else
                                        {{ date('d M Y, H:i', strtotime($owner->last_signed)) }}
                                    @endif
                                </td>
                                <td>
                                    <!-- <div class="dropdown">
                                        <button class="button_ico dropdown-toggle" data-toggle="dropdown" disabled>
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                    </div> -->
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    
                    @if(count($clientuser) > 0)
                        @foreach($clientuser as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" id="checkbox"/>
                                </td>
                                <td>
                                    <a href="{{ route('adminuser.access-users.detail', $user->user_id) }}">
                                        @if($user->RefUser->name != "null")
                                            {!! \globals::get_user_avatar_small($user->name, $user->RefUser->avatar_color) !!}
                                            {{ $user->name }}
                                        @else
                                            {!! \globals::get_user_avatar_small($user->email_address, $user->RefUser->avatar_color) !!}
                                            {{ $user->email_address }}
                                        @endif
                                    </a>
                                </td>
                                <td>
                                    @if($user->role == 0)
                                        <span class="you_status">All</span>
                                    @else
                                        @php $grups = DB::table('assign_user_group')->select('access_group.group_name')->join('access_group', 'assign_user_group.group_id', 'access_group.group_id')->where('assign_user_group.user_id', $user->user_id)->where('assign_user_group.client_id', \globals::get_client_id())->get() @endphp
                                        @foreach($grups as $grup)
                                            <span class="invited_status">{{ $grup->group_name }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if($user->role == 0) 
                                        Administrator
                                    @elseif($user->role == 1)
                                        Collaborator
                                    @elseif($user->role == 2)
                                        Client
                                    @endif
                                </td>
                                <td>
                                    @if($user->email_address == Auth::User()->email)
                                        <span class="active_status">You</span>
                                    @elseif($user->status == 1)
                                        <span class="active_status">Active</span>
                                    @elseif($user->status == 2)
                                        <span class="disabled_status">Disabled</span>
                                    @elseif($user->status == 0)
                                        <span class="invited_status">Invited</span>
                                    @endif
                                </td>
                                <td>
                                    @if(is_null(App\Models\User::where('email', $user->email_address)->value('last_signed')))
                                        -
                                    @else
                                        {{ date('d M Y, H:i', strtotime(App\Models\User::where('email', $user->email_address)->value('last_signed'))) }}
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-top pull-right">
                                            @if($user->role == \globals::set_role_client())
                                                <li><a href="#modal-move-group" data-toggle="modal" onclick="moveGroup('{{ $user->user_id }}')">Move to group</a></li>
                                            @endif

                                            @if($user->status == 1)
                                                <li><a href="#modal-disabled-user" data-toggle="modal" data-url="{{ route('adminuser.access-users.disable-user', base64_encode($user->email_address)) }}" onclick="getUrlDisableUser(this)">Disable User</a></li>
                                            @elseif($user->status == 2)
                                                <li><a href="#modal-enable-user" data-toggle="modal" data-url="{{ route('adminuser.access-users.enable-user', base64_encode($user->email_address)) }}" onclick="getUrlEnableUser(this)">Enable User</a></li>
                                            @endif

                                            @if($user->status == 0)
                                                <li><a href="{{ route('adminuser.access-users.resend-email', base64_encode($user->email_address)) }}"></i>Resend invitation email</a></li>
                                            @endif
                                            <li><a href="#modal-delete-user" data-toggle="modal" data-url="{{ route('adminuser.access-users.delete-user', base64_encode($user->email_address)) }}" onclick="getUrlDeleteUser(this)" style="color:#D92D20;">Delete User</a></li>
                                        </ul>
                                    </div>
                                    
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    @elseif(request()->input('tab') == "group")
        <div id="table">
            @if(count($listGroup) > 0)
                <table id="tableUser">
                    <thead>
                        <tr>
                            <th id="check"><input type="checkbox" id="checkbox" disabled/></th>
                            <th id="name">Name</th>
                            <th id="members">Members</th>
                            <th id="status">&nbsp;Status</th>
                            <th id="navigationdot">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listGroup as $list)
                            <tr>
                                <td>
                                    <input type="checkbox" id="checkbox" />
                                </td>
                                <td>
                                    <a href="{{ route('adminuser.access-users.detail-group', $list->group_id) }}">
                                        <img src="{{ url('template/images/group.png') }}" width="24" height="24"> {{ $list->group_name }}
                                    </a>
                                </td>
                                <td>{{ $list->RefAssignUserGroup->count() }}</td>
                                <td width="150">
                                    @if($list->group_status == 1)
                                        <span class="active_status"> Active</span>
                                    @elseif($list->group_status == 2)
                                        <span class="disabled_status"> Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-top pull-right">
                                            @if($list->group_status == 1)
                                                <li><a href="#modal-disabled-group" data-toggle="modal" data-url="{{ route('adminuser.access-users.disabled-group', $list->group_id) }}" onclick="getUrlDisableGroup(this)">Disable access</a></li>
                                            @elseif($list->group_status == 2)
                                                <li><a href="#modal-enable-group" data-toggle="modal" data-url="{{ route('adminuser.access-users.enable-group', $list->group_id) }}" onclick="getUrlEnableGroup(this)">Enable access</a></li>
                                            @endif
                                            <li><a href="#modal-delete-group" data-toggle="modal" data-url="{{ route('adminuser.access-users.delete-group', $list->group_id) }}" onclick="getUrlDeleteGroup(this)" style="color:#D92D20;">Delete Group</a></li>
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
                        <img src="{{ url('template/images/empty_group.png') }}" width="300" />
                    </center>    
                </div>
            @endif
        </div>
    @endif

    <div id="modal-invite-users" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                	<div class="custom-modal-header">
                		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                		<div style="float: left;">
                            <img id="inviteuser-ico" src="{{ url('template/images/icon_menu/invite_user.png') }}" width="24" height="24">
	                    </div>
	                    <div style="float: left; margin-left: 10px;">
	                        <h4 class="modal-title" id="titleModal">
                                Invite users
	                        </h4>
	                    </div>
	                </div>
                </div>
                <div class="modal-body">
                    <form action="{{ route('adminuser.access-users.create')}}" method="POST">
                        @csrf
                        <h5 class="userform">Email address <span class="text-danger">*</span></h5>
                        <div class="tags-default tag-email">
                            <input name="email_address" id="email_address" placeholder="Enter email address" required/>
                        </div>
                        
                        <h5 class="userrole">Role <span class="text-danger">*</span></h5>
                        <div class="roleselect">
                            <input type="radio" name="role" value="0" onclick="setRole(this)" required>
                            <div class="roledetail">
                                <p class="roletitle">Administrator<p>
                                <p class="roledesc">Have full access to manage documents, QnA contents, invite users and access to all reports.</p>
                            </div>
                        </div>

                        <div class="roleselect">
                            <input type="radio" name="role" value="1" onclick="setRole(this)" required>
                            <div class="roledetail">
                                <p class="roletitle">Collaborator<p>
                                <p class="roledesc">Can view, download, and ask questions based on their group permissions.</p>
                            </div>
                        </div>

                        <div class="roleselect">
                            <input type="radio" name="role" value="2" onclick="setRole(this)" required>
                            <div class="roledetail">
                                <p class="roletitle">Client<p>
                                <p class="roledesc">Can view, download, and ask questions based on their group permissions.</p>
                            </div>
                        </div>
                        
                        <div id="data_group" class="company_id">
                            <h5 class="usercompany">Group</h5>
                            <select class="form-control select2" data-placeholder="Select group" multiple name="group[]">
                                @foreach($group as $groups)
                                    <option value="{{ $groups->group_id }}">{{ $groups->group_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="resultGroup"></div>
                        <div id="data_project">
                            <h5 class="usercompany">Project <span class="text-danger">*</span></h5>
                            <select class="form-control select2" data-placeholder="Select Project" multiple name="project[]" id="projectsID">
                                @foreach($project as $projects)
                                    @if(count($projects->RefSubProject) > 0)
                                        <optgroup label="{{ $projects->project_name }}">
                                            @foreach($projects->RefSubProject as $subProjects)
                                                <option value="{{$subProjects->subproject_id}}">{{ $subProjects->subproject_name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div id="resultProject"></div>
                        <div class="formbutton">
                            <a class="cancelbtn" data-dismiss="modal">Cancel</a>
                            <button class="createbtn" type="submit">Invite</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @include('adminuser.users.move_group')
    @include('adminuser.users.create_group')

    @include('adminuser.users.modal_disable_group')
    @include('adminuser.users.modal_enable_group')
    @include('adminuser.users.modal_delete_group')

    @include('adminuser.users.modal_disable_user')
    @include('adminuser.users.modal_enable_user')
    @include('adminuser.users.modal_delete_user')

    @push('scripts')
    <script>
        $('#email_address').tagsinput();

		$(document).ready(function () {
            $('#tableUser').dataTable({
                "bPaginate": true,
                "bInfo": false,
                "bSort": false,
                "dom": 'rtip',
                "stripeClasses": false,
                "pageLength": 8,
            });

            $('#search_bar').keyup(function() {
                var table = $('#tableUser').DataTable();
                table.search($(this).val()).draw();
            });
        });

        function hideNotification() {
            setTimeout(function() {
                $('#notification').fadeOut();
                }, 2000);
        };

        hideNotification();

        function moveGroup(email) {
            // document.getElementById('moveuser').style.display = 'block';
            document.getElementById('user_id').value = email;
        };

        function setRole(element) {
            if(element.value == "{{ \globals::set_role_administrator() }}"){
                /* jika yg dipilih administrator group & project menjd all */
                $("#data_group").css("display", "none");
                $("#data_project").css("display", "none");
                $("#projectsID").prop("required", false);

                var resGroup = "";
                var resGroup = "<div class='form-group'>";
                    resGroup += "<label>Group</label>"
                    resGroup += "<select class='form-control' disabled>"
                        resGroup += "<option value='all'>All</option>"
                    resGroup += "</select>"
                resGroup += "</div>"

                var resProject = "";
                var resProject = "<div class='form-group'>";
                    resProject += "<label>Project</label>"
                    resProject += "<select class='form-control' disabled>"
                        resProject += "<option value='all'>All</option>"
                    resProject += "</select>"
                resProject += "</div>"

                $("#resultGroup").html(resGroup);
                $("#resultProject").html(resProject);
            }else if(element.value == "{{ \globals::set_role_collaborator() }}"){
                /* jika yg dipilih collaborator group hilang */
                $("#data_group").css("display", "none");
                $("#data_project").css("display", "block");
                $("#projectsID").prop("required", true);
                $("#resultGroup").html("");
                $("#resultProject").html("");
            }else{
                $("#data_group").css("display", "block");
                $("#data_project").css("display", "block");
                $("#projectsID").prop("required", true);
                $("#resultGroup").html("");
                $("#resultProject").html("");
            }
        }

        /* group */
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

        /* user */
        function getUrlDisableUser(element) {
            var url = $(element).data('url');
            $("#get_url_disable_user").val(url);
        }

        function actDisableUser() {
            var getUrlDisabled = $("#get_url_disable_user").val();
            if (getUrlDisabled != 'undefined') {
                window.location.href = getUrlDisabled;
            }
        }

        function getUrlEnableUser(element) {
            var url = $(element).data('url');
            $("#get_url_enable_user").val(url);
        }

        function actEnableUser() {
            var getUrlEnable = $("#get_url_enable_user").val();
            if (getUrlEnable != 'undefined') {
                window.location.href = getUrlEnable;
            }
        }

        function getUrlDeleteUser(element) {
            var url = $(element).data('url');
            $("#get_url_delete_user").val(url);
        }

        function actDeleteUser() {
            var getUrlDelete = $("#get_url_delete_user").val();
            if (getUrlDelete != 'undefined') {
                window.location.href = getUrlDelete;
            }
        }
    </script>
    @endpush
@endsection