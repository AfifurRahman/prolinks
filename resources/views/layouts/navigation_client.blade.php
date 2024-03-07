<div class="left side-menu" style="background: #F1F5F9; padding: 10px;">
    <div class="sidebar-inner slimscrollleft">
        <div id="sidebar-menu">
            <form id="app-change-project" action="{{ route('project.change-main-project') }}" method="POST">
                @csrf
                
                <div class="input-group" style="margin-top:5px;">
                    <div class="input-group-btn">
                        <button class="btn btn-primary" style="height:38px;width:38px;" title="add new project" data-target="#modal-new-project" data-toggle="modal" type="button">
                            <i class="fa fa-plus-circle"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <select name="main_project_id" id="main_project_id" class="form-control" style="background: transparent; border: solid 1px #CCC; border-radius:0px 5px 5px 0px;">
                            @if(count(\globals::get_project_sidebar()) > 0)
                                @foreach(\globals::get_project_sidebar() as $mainProject)
                                    <option value="{{ $mainProject->project_id }}" {{ !empty(\globals::get_project_id()) && \globals::get_project_id() == $mainProject->project_id ? "selected":"" }} >{{ $mainProject->project_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

            </form>
            <ul>
                <li class="menu-title">MAIN MENU</li>
                <li>
                    <a href="/" class="waves-effect"><i class="mdi mdi-view-dashboard"></i><span> Activities </span></a>
                </li>
                <!-- <li>
                    <a href="{{ route('company.list-company') }}" class="waves-effect"><i class="fa fa-users"></i><span> Companies </span></a>
                </li> -->
                <li>
                    <a href="{{ route('adminuser.access-users.list') }}" class="waves-effect"><i class="fa fa-key"></i><span> Access Users </span></a>
                </li>
                <li>
                    <a href="{{ route('adminuser.documents.list') }}" class="waves-effect"><i class="fa fa-cogs"></i><span> Documents </span></a>
                </li>
                <li>
                    <a href="{{ route('discussion.list-discussion') }}" class="waves-effect"><i class="fa fa-comments"></i><span> Q & A </span></a>
                </li>
                <li>
                    <a href="xxx" class="waves-effect"><i class="fa fa-list-alt"></i><span> Reports </span></a>
                </li>
            </ul>
        </div>
    </div>
</div>