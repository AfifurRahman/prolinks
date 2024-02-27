<div class="left side-menu" style="background: #F1F5F9; padding: 10px;">
    <div class="sidebar-inner slimscrollleft">
        <div id="sidebar-menu">
            <form id="app-change-project" action="{{ route('project.change-main-project') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-top: 10px;">
                    <select name="main_project_id" id="main_project_id" class="form-control" style="background: transparent; border: solid 1px #CCC;">
                        @if(count(\globals::get_project_sidebar()) > 0)
                            @foreach(\globals::get_project_sidebar() as $mainProject)
                                <option value="{{ $mainProject->project_id }}" {{ !empty(Session::get('project_id')) && Session::get('project_id') == $mainProject->project_id ? "selected":"" }} >{{ $mainProject->project_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </form>
            <ul>
                <li class="menu-title">MAIN MENU</li>
                <li>
                    <a href="/" class="waves-effect"><i class="mdi mdi-view-dashboard"></i><span> Activities </span></a>
                </li>
                <li>
                    <a href="{{ route('company.list-company') }}" class="waves-effect"><i class="fa fa-users"></i><span> Companies </span></a>
                </li>
                <li>
                    <a href="{{ route('adminuser.access-users.list') }}" class="waves-effect"><i class="fa fa-key"></i><span> Access Users </span></a>
                </li>
                <li>
                    <a href="{{ route('project.list-project') }}" class="waves-effect"><i class="fa fa-cogs"></i><span> Project </span></a>
                </li>
                <li>
                    <a href="xxx" class="waves-effect"><i class="fa fa-comments"></i><span> Q & A </span></a>
                </li>
                <li>
                    <a href="xxx" class="waves-effect"><i class="fa fa-list-alt"></i><span> Reports </span></a>
                </li>
            </ul>
        </div>
        <div style="position: absolute; bottom: 0; margin-bottom: 40px; border-top: solid 1px #CCC; padding: 10px;">
            <div class="btn-group dropup">
                <button type="button" class="btn" style="border: solid 0px; background: transparent;">
                    <div class="auth-client-name">
                        <h5 data-toggle="tooltip" data-placement="top" title="{{ Auth::user()->name }}">{!! Str::limit(Auth::user()->name, 18) !!}</h5>
                        <span class="text-muted">
                            {{ Auth::user()->email }}
                        </span>
                    </div>
                </button>
                <button type="button" class="btn dropdown-toggle" style="border: solid 0px; background: transparent;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v" style="font-size: 1.5em; padding-top: 15px;"></i>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="ti-power-off m-r-5"></i> {{ __('Logout') }}</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>