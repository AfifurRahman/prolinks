<div class="left side-menu" style="background: #F1F5F9; padding: 10px; position:fixed;">
    <div class="sidebar-inner slimscrollleft">
        <div id="sidebar-menu">
            <form id="app-change-project" action="{{ route('project.change-main-project') }}" method="POST">
                @csrf
                <div class="project-group" style="margin-top:10px;">
                    @if(in_array(\globals::set_role_collaborator(), \role::get_role_client()) OR in_array(\globals::set_role_client(), \role::get_role_client()))
                        <div class="form-group">
                            <select name="main_project_id" id="main_project_id" class="form-control" style="background: transparent; border: solid 1px #CCC; border-radius:0px 5px 5px 0px;">
                                @if(count(\globals::get_project_sidebar()) > 0)
                                    @foreach(\globals::get_project_sidebar() as $mainProject)
                                        @if(!empty($mainProject->RefAssignProject) && count($mainProject->RefAssignProject) > 0)
                                            <optgroup label="{{ $mainProject->project_name }}">
                                                @if(count($mainProject->RefAssignProject) > 0)
                                                    @foreach($mainProject->RefAssignProject as $subsProj)
                                                        <option value="{{ $subsProj->subproject_id }}" {{ !empty(Auth::user()->session_project) && Auth::user()->session_project == $subsProj->subproject_id ? "selected":"" }} >{{ $subsProj->RefSubProject->subproject_name }}</option>
                                                    @endforeach
                                                @endif
                                            </optgroup>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @endif
                </div>
            </form>
            @php
                $uri = Request::segment(1);
                
                $active_dashboard = "";
                if($uri == null){
                    $active_dashboard = "active text-active";
                }

                $active_users = "";
                if($uri == "users"){
                    $active_users = "active text-active";
                }

                $active_project = "";
                if($uri == "project" OR $uri == "documents"){
                    $active_project = "active text-active";
                }

                $active_discussion = "";
                if($uri == "discussion"){
                    $active_discussion = "active text-active";
                }

                $active_setting = "";
                if($uri == "setting"){
                    $active_setting = "active text-active";
                }
            @endphp
            <ul style="margin-top:14px">
                <li class="menu-title">MAIN MENU</li>
                @if(Auth::user()->type == \globals::set_role_administrator())
                    <li>
                        <a href="/" class="waves-effect {{ $active_dashboard }}"><img src="{{ url('template/images/icon_menu/dashboard.png') }}" width="20" height="20"><span class="{{ $active_dashboard }}"> Activities </span></a>
                    </li>
                    <li>
                        <a href="{{ route('adminuser.access-users.list', 'tab=user') }}" class="waves-effect {{ $active_users }}"><img src="{{ url('template/images/icon_menu/group.png') }}" width="20" height="20"><span class="{{ $active_users }}"> Access Users </span></a>
                    </li>
                    <li>
                        <a href="{{ route('project.list-project') }}" class="waves-effect {{ $active_project }}"><img src="{{ url('template/images/icon_menu/briefcase.png') }}" width="20" height="20"><span class="{{ $active_project }}"> Project </span></a>
                    </li>
                    <li>
                        <a href="{{ route('discussion.list-discussion') }}" class="waves-effect {{ $active_discussion }}"><img src="{{ url('template/images/icon_menu/question.png') }}" width="20" height="20"><span class="{{ $active_discussion }}"> Q & A </span></a>
                    </li>
                @elseif(Auth::user()->type == \globals::set_role_collaborator() || Auth::user()->type == \globals::set_role_client())
                    <li>
                        @php
                            $subProject = App\Models\SubProject::where('subproject_id', Auth::user()->session_project)->first();
                        @endphp
                        <a href="{{ route('adminuser.documents.list', base64_encode($subProject->project_id.'/'.$subProject->subproject_id)) }}" class="waves-effect {{ $active_project }}"><img src="{{ url('template/images/icon_menu/folder.png') }}" width="20" height="20"><span class="{{ $active_project }}"> Documents </span></a>
                    </li>
                    <li>
                        <a href="{{ route('discussion.list-discussion') }}" class="waves-effect {{ $active_discussion }}"><img src="{{ url('template/images/icon_menu/question.png') }}" width="20" height="20"><span class="{{ $active_discussion }}"> Q & A </span></a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('setting', 'tab=email_setting') }}" class="waves-effect {{ $active_setting }}"><img src="{{ url('template/images/icon_menu/settings.png') }}" width="20" height="20"><span class="{{ $active_setting }}"> Settings </span></a>
                </li>
            </ul>
        </div>
    </div>
</div>