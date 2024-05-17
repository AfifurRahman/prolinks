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

        .modal-content {
            -webkit-border-radius: 0px !important;
            -moz-border-radius: 0px !important;
            border-radius: 10px !important; 
        }
    </style>
    <div class="header-detail">
        <div class="pull-left">
            <h2 id="title" style="color:black;font-size:28px;">{{ !empty($clientuser->name) ? $clientuser->name : $clientuser->email_address }}</h2>
        </div>
        <div class="pull-right">
            <div class="dropdown" style="margin-top:10px; z-index:99;">
                <button class="btn btn-md dropdown-toggle" type="button" data-toggle="dropdown" style="color:#1570EF; font-weight:bold; background:transparent; border:solid 1px #EDF0F2; border-radius:8px;">
                    Actions&nbsp; <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    @if($clientuser->role == \globals::set_role_client())
                        <li><a href="#modal-move-group" data-toggle="modal" onclick="moveGroup('{{ $clientuser->user_id }}')">Move to group</a></li>
                    @endif

                    @if($clientuser->status == 0)
                        <li><a href="{{ route('adminuser.access-users.resend-email', base64_encode($clientuser->email_address)) }}"></i>Resend invitation email</a></li>
                    @endif

                    @if($clientuser->status == 1)
                        <li><a href="#modal-disabled-user" data-toggle="modal" data-url="{{ route('adminuser.access-users.disable-user', base64_encode($clientuser->email_address)) }}" onclick="getUrlDisableUser(this)">Disable User</a></li>
                    @elseif($clientuser->status == 2)
                        <li><a href="#modal-enable-user" data-toggle="modal" data-url="{{ route('adminuser.access-users.enable-user', base64_encode($clientuser->email_address)) }}" onclick="getUrlEnableUser(this)">Enable User</a></li>
                    @endif
                    <div class="divider"></div>
                    <li><a href="#modal-delete-user" data-toggle="modal" data-url="{{ route('adminuser.access-users.delete-user', base64_encode($clientuser->email_address)) }}" onclick="getUrlDeleteUser(this)" style="color:#D92D20;">Delete User</a></li>
                </ul>
            </div>
        </div> <div style="clear:both;"></div>
    </div>
    <div class="company-detail">
        <form action="{{ route('adminuser.access-users.edit', $clientuser->user_id) }}" method="POST">
            @csrf
            <table id="formEditClients" style="display:none;" class="table borderless">
                <tr>
                    <td colspan="2"><h3>User Information </h3></td>
                </tr>
                <tr>
                    <td width="130">Name <span class="text-danger">*</span></td>
                    <td width="500">
                        <input required type="text" name="name" value="{{ !empty($clientuser->name) ? $clientuser->name : '' }}" class="form-control" />
                    </td>
                </tr>
                <tr>
                    <td>Company</td>
                    <td>
                        <input type="text" name="company" value="{{ !empty($clientuser->company) ? $clientuser->company : '' }}" class="form-control" />
                    </td>
                </tr>
                <tr>
                    <td>Job Title</td>
                    <td>
                        <input type="text" name="job_title" value="{{ !empty($clientuser->job_title) ? $clientuser->job_title : '' }}" class="form-control" />
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="button" onclick="closeEditUser()" class="btn btn-default">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save changes
                        </button>
                    </td>
                </tr>
            </table>
        </form>
        <table id="listClients" class="table borderless">
            <tr>
                <td colspan="2"><h3>User Information <img src="{{ url('template/images/edit.png') }}" width="22" height="22" style="cursor:pointer;" onclick="editUser()" /></h3> </td>
            </tr>
            <tr>
                <td width="150">Status</td>
                <td>
                    @if($clientuser->email_address == Auth::User()->email)
                        <span class="active_status">You</span>
                    @elseif($clientuser->status == 1)
                        <span class="active_status">Active</span>
                    @elseif($clientuser->status == 2)
                        <span class="disabled_status">Disabled</span>
                    @elseif($clientuser->status == 0)
                        <span class="invited_status">Invited</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Name</td>
                <td>{!! !empty($clientuser->name) ? $clientuser->name : '<span class="not-set">not set</span>' !!}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{!! !empty($clientuser->email_address) ? $clientuser->email_address : '<span class="not-set">not set</span>' !!}</td>
            </tr>
            <tr>
                <td>Company</td>
                <td>{!! !empty($clientuser->company) ? $clientuser->company : '<span class="not-set">not set</span>' !!}</td>
            </tr>
            <tr>
                <td>Job Title</td>
                <td>{!! !empty($clientuser->job_title) ? $clientuser->job_title : '<span class="not-set">not set</span>' !!}</td>
            </tr>
            <tr>
                <td>Last signed in</td>
                <td>
                    @if(is_null($clientuser->last_signed))
                        -
                    @else
                        {{ date('d M Y, H:i', strtotime($clientuser->last_signed)) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Invite By</td>
                <td>
                    {!! !empty($clientuser->RefCreatedName->name) ? $clientuser->RefCreatedName->name : '' !!}
                </td>
            </tr>
        </table>
        
        <form action="{{ route('adminuser.access-users.edit-role', $clientuser->user_id) }}" method="POST">
            @csrf
            <table id="formEditRole" style="display:none;" class="table borderless">
                <tr>
                    <td colspan="2"><h3>Role & Group</h3></td>
                </tr>
                <tr>
                    <td width="150">Role <span class="text-danger">*</span></td>
                    <td width="500">
                        <select name="role" onchange="setRole(this)" class="form-control">
                            <option value="0" {{ !empty($clientuser->role) && $clientuser->role == 0 ? "selected":"" }}>Administrator</option>
                            <option value="1" {{ !empty($clientuser->role) && $clientuser->role == 1 ? "selected":"" }}>Collaborator</option>
                            <option value="2" {{ !empty($clientuser->role) && $clientuser->role == 2 ? "selected":"" }}>Client</option>
                        </select>
                    </td>
                </tr>
                <tr id="data_group">
                    <td>Group</td>
                    <td>
                        <select class="form-control select2" id="group" data-placeholder="Select Group" multiple name="group[]">
                            @foreach($group as $groups)
                                <option value="{{ $groups->group_id }}" {{ in_array($groups->group_id, $groupDetail) ? "selected":"" }}>{{ $groups->group_name }}</option>
                            @endforeach
                        </select>
                        <div id="resultGroup"></div>
                    </td>
                </tr>
                <tr id="data_project">
                    <td>Project <span class="text-danger">*</span></td>
                    <td>
                        <select class="form-control select2" id="project" data-placeholder="Select Project" multiple name="project[]">
                            @foreach($project as $projects)
                                <optgroup label="{{ $projects->project_name }}">
                                    @if(count($projects->RefSubProject) > 0)
                                        @foreach($projects->RefSubProject as $subProjects)
                                            <option value="{{$subProjects->subproject_id}}" {{ in_array($subProjects->subproject_id, $projectDetail) ? "selected":"" }}>{{ $subProjects->subproject_name }}</option>
                                        @endforeach
                                    @endif
                                </optgroup>
                            @endforeach
                        </select>
                        <div id="resultProject"></div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="button" onclick="closeEditRole()" class="btn btn-default">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save changes
                        </button>
                    </td>
                </tr>
            </table>
        </form>
        <table id="listRole" class="table borderless">
            <tr>
                <td colspan="2"><h3>Role & Group <img src="{{ url('template/images/edit.png') }}" width="22" height="22" style="cursor:pointer;" onclick="editRole()" /></h3></td>
            </tr>
            <tr>
                <td width="150">Role</td>
                <td>
                    @if($clientuser->role == 0) 
                        Administrator
                    @elseif($clientuser->role == 1)
                        Collaborator
                    @elseif($clientuser->role == 2)
                        Client
                    @endif
                </td>
            </tr>
            <tr>
                <td>Group</td>
                <td>
                    @if($clientuser->role == 0)
                        <a class="btn btn-default">All</a>
                    @else
                        @php 
                            $grups = DB::table('assign_user_group')->select('access_group.group_name')->join('access_group', 'assign_user_group.group_id', 'access_group.group_id')->where('assign_user_group.client_id', \globals::get_client_id())->where('assign_user_group.user_id', $clientuser->user_id)->get() 
                        @endphp
                        @foreach($grups as $grup)
                            <a class="btn btn-default"><img src="{{ url('template/images/icon_menu/group.png') }}" width="20" height="20"> {{ $grup->group_name }}</a>
                        @endforeach
                    @endif
                </td>
            </tr>
            <tr>
                <td>Project</td>
                <td>
                    @if($clientuser->role == 0)
                        <a class="btn btn-default">All</a>
                    @else
                        @php
                            $projects = App\Models\AssignProject::where('user_id', $clientuser->user_id)->where('client_id', $clientuser->client_id)->get();
                        @endphp
                        @foreach($projects as $project)
                            <div style="margin-bottom:5px;">
                                <a class="btn btn-default"><img src="{{ url('template/images/data-project.png') }}" width="20" height="20"> {{ $project->RefProject->project_name }} ( {{ $project->RefSubProject->subproject_name }} )</a>
                            </div>
                        @endforeach
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @include('adminuser.users.move_group')
    @include('adminuser.users.modal_disable_user')
    @include('adminuser.users.modal_enable_user')
    @include('adminuser.users.modal_delete_user')
@stop

@push('scripts')
    <script>
        function editUser() {
            $("#formEditClients").css("display", "block");
            $("#listClients").css("display", "none");
        }

        function closeEditUser() {
            $("#formEditClients").css("display", "none");
            $("#listClients").css("display", "block");
        }

        function editRole() {
            var role = $('[name="role"]').val();
            if (role == 0) {
                $("#data_group").hide();
                $("#data_project").hide();
            }else if(role == 1){
                $("#data_group").hide();
                $("#data_project").show();
            }else{
                $("#data_group").show();
                $("#data_project").show();
            }
            
            $("#formEditRole").css("display", "block");
            $("#listRole").css("display", "none");
        }

        function closeEditRole() {
            $("#formEditRole").css("display", "none");
            $("#listRole").css("display", "block");
        }

        function setRole(element) {
            if(element.value == 0){
                /* jika yg dipilih administrator group & project menjd all */
                $("#data_group").hide();
                $("#data_project").hide();
                $("#group").prop("required", false);
                $("#project").prop("required", false);

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
            }else if(element.value == 1){
                $("#data_group").hide();
                $("#data_project").show();
                $("#group").prop("required", false);
                $("#project").prop("required", true);

                $("#resultGroup").html("");
                $("#resultProject").html("");
            }else if(element.value == 2){
                $("#data_group").show();
                $("#data_project").show();
                $("#resultGroup").html("");
                $("#resultProject").html("");
                $("#group").prop("required", false);
                $("#project").prop("required", true);
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

        function moveGroup(email) {
            // document.getElementById('moveuser').style.display = 'block';
            document.getElementById('user_id').value = email;
        };

        function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();
    </script>
@endpush