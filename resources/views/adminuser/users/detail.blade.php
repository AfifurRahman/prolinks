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
            <h2 id="title" style="color:black;font-size:28px;">{{ !empty($clientuser->name) ? $clientuser->name : $clientuser->RefUser->email }}</h2>
        </div>
        <div class="pull-right">
            <div class="dropdown" style="margin-top:10px; z-index:99;">
                <button class="btn btn-md dropdown-toggle" type="button" data-toggle="dropdown" style="color:#1570EF; font-weight:bold; background:transparent; border:solid 1px #EDF0F2; border-radius:8px;">
                    Actions&nbsp; <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="#modal-add-project" data-toggle="modal" data-title="Edit Project">Move to group</a></li>
                    <li><a href="">Disable access</a></li>
                    <li><a href="">Delete user</a></li>
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
                    <td width="130">Name</td>
                    <td width="500">
                        <input required type="text" name="name" value="{{ !empty($clientuser->name) ? $clientuser->name : '' }}" class="form-control" />
                    </td>
                </tr>
                <tr>
                    <td>Company</td>
                    <td>
                        <input required type="text" name="company" value="{{ !empty($clientuser->company) ? $clientuser->company : '' }}" class="form-control" />
                    </td>
                </tr>
                <tr>
                    <td>Job Title</td>
                    <td>
                        <input required type="text" name="job_title" value="{{ !empty($clientuser->job_title) ? $clientuser->job_title : '' }}" class="form-control" />
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
                    <td width="150">Role</td>
                    <td width="500">
                        <select name="role" onchange="setRole(this)" class="form-control">
                            <option value="0" {{ !empty($clientuser->role) && $clientuser->role == 0 ? "selected":"" }}>Administrator</option>
                            <option value="1" {{ !empty($clientuser->role) && $clientuser->role == 1 ? "selected":"" }}>Collaborator</option>
                            <option value="2" {{ !empty($clientuser->role) && $clientuser->role == 2 ? "selected":"" }}>Client</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Group</td>
                    <td>
                        @if($clientuser->role == 0)
                            <select class="form-control" disabled>
                                <option>All</option>
                            </select>
                        @else
                            <select class="form-control select2" data-placeholder="Unassigned" multiple name="group[]">
                                @foreach($group as $groups)
                                    @if($groups == 0)
                                        <option value="0">Unassigned</option>
                                    @else
                                        <option value="{{$groups}}" {{ in_array($groups, $groupDetail) ? "selected":"" }}>{{ DB::table('access_group')->where('group_id', $groups)->value('group_name') }}</option>
                                    @endif
                                @endforeach
                            </select>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Project</td>
                    <td>
                        @if($clientuser->role == 0)
                            <select class="form-control" disabled>
                                <option>All</option>
                            </select>
                        @else
                            <select class="form-control select2" data-placeholder="Unassigned" multiple name="project[]">
                                @foreach($project as $projects)
                                    @if($projects == 0)
                                        <option value="0">Unassigned</option>
                                    @else
                                        <option value="{{$projects}}" {{ in_array($projects, $projectDetail) ? "selected":"" }}>{{ DB::table('project')->where('project_id', $projects)->value('project_name') }}</option>
                                    @endif
                                @endforeach
                            </select>
                        @endif
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
                        <span class="label label-default">All</span>
                    @else
                        @php $grups = DB::table('assign_user_group')->select('access_group.group_name')->join('access_group', 'assign_user_group.group_id', 'access_group.group_id')->where('assign_user_group.user_id', $clientuser->user_id)->where('assign_user_group.client_id', \globals::get_client_id())->get() @endphp
                        @foreach($grups as $grup)
                            <span class="label label-default">{{ $grup->group_name }}</span>
                        @endforeach
                    @endif
                </td>
            </tr>
            <tr>
                <td>Project</td>
                <td>
                    @if($clientuser->role == 0)
                        <span class="label label-default">All</span>
                    @else
                        @php $projects = DB::table('assign_project')->select('project.project_name')->join('project', 'project.project_id', 'assign_project.project_id')->where('assign_project.user_id', $clientuser->user_id)->where('assign_project.client_id', \globals::get_client_id())->get() @endphp
                        @foreach($projects as $project)
                            <span class="label label-default">{{ $project->project_name }}</span>
                        @endforeach
                    @endif
                </td>
            </tr>
        </table>
    </div>
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
                $("#data_group").css("display", "none");
                $("#data_project").css("display", "none");

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
            }else{
                $("#data_group").css("display", "block");
                $("#data_project").css("display", "block");
                $("#resultGroup").html("");
                $("#resultProject").html("");
            }
        }

        function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();
    </script>
@endpush