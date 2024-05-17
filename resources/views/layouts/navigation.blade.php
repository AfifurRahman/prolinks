@php
    use App\Models\AdminBackend;
    $admin = AdminBackend::where('superuser_id', Auth::guard('backend')->user()->superuser_id)->first();

    $userRole = [];
    if(!empty($admin->RefRole->access)){
        $userRole = json_decode($admin->RefRole->access, TRUE);
    }

    $pages = request()->input('page');

    $uri = Request::segment(2);
    $urisub1 = Request::segment(3);
    $urisub2 = Request::segment(4);

    $active_dashboard = "";
    $display_dashboard = "";

    $active_pricing = "";
    $display_pricing = "";

    $active_client = "";
    $display_client = "";

    $active_access_users = "";
    $display_access_users = "";

    $active_monitoring = "";
    $display_monitoring = "";

    if($uri != NULL){
        
        if($uri == "dashboard"){
            $active_dashboard = "active";
            $display_dashboard = "block";
        }

        if($uri == "pricing"){
            $active_pricing = "active";
            $display_pricing = "block";
        }

        if($uri == "client"){
            $active_client = "active";
            $display_client = "block";
        }

        if($uri == "access-users"){
            $active_access_users = "active";
            $display_access_users = "block";
        }

        if($uri == "monitoring"){
            $active_monitoring = "active";
            $display_monitoring = "block";
        }
    }
@endphp

<div class="left side-menu" style="background: #F1F5F9; padding: 10px;">
    <div class="sidebar-inner slimscrollleft">
        <div id="sidebar-menu">
            <ul>
                <li class="menu-title">MAIN MENU</li>
                @if(in_array("dashboard", $userRole))
                    <li>
                        <a href="{{ route('backend.dashboard') }}" class="waves-effect"><i class="mdi mdi-view-dashboard"></i><span> Dashboard </span></a>
                    </li>
                @endif

                @if(in_array("list-pricing", $userRole))
                    <li>
                        <a href="{{ route('backend.pricing.list') }}" class="waves-effect"><i class="mdi mdi-calculator"></i><span> Pricing </span></a>
                    </li>
                @endif

                @if(in_array("list-client", $userRole))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-account-location"></i><span> Manage Client </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('backend.client.list') }}">Client</a></li>
                        </ul>
                    </li>
                @endif

                @if(in_array("list-role", $userRole) OR in_array("list-users", $userRole))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-account-key"></i><span> Access Users </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if(in_array("list-role", $userRole))
                                <li><a href="{{ route('backend.access-users.role') }}">Role</a></li>
                            @endif

                            @if(in_array("list-users", $userRole))
                                <li><a href="{{ route('backend.access-users.admin-management') }}">Admin Management</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if(in_array("list-monitoring", $userRole))
                    <li>
                        <a href="{{ route('backend.monitoring.list') }}" class="waves-effect"><i class="mdi mdi-laptop-mac"></i><span> Monitoring </span></a>
                    </li>
                @endif

                @if(in_array("log-activity", $userRole))
                    <li>
                        <a href="{{ route('backend.log.list') }}" class="waves-effect"><i class="fa fa-history"></i><span> Log Activity </span></a>
                    </li>
                @endif
            </ul>
            
            
        </div>
        <div style="position: absolute; bottom: 0; margin-bottom: 40px; border-top: solid 1px #CCC; padding: 10px;">
            <div class="btn-group dropup">
                <button type="button" class="btn" style="border: solid 0px; background: transparent;">
                    <div class="auth-client-name">
                        <h5 data-toggle="tooltip" data-placement="top" title="{{ Auth::guard('backend')->user()->first_name." ".Auth::guard('backend')->user()->last_name }}">{!! Str::limit(Auth::guard('backend')->user()->first_name." ".Auth::guard('backend')->user()->last_name, 18) !!}</h5>
                        <span class="text-muted">
                            {{ !empty($admin->RefRole->role_name) ? $admin->RefRole->role_name : '-' }}
                        </span>
                    </div>
                </button>
                <button type="button" class="btn dropdown-toggle" style="border: solid 0px; background: transparent;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v" style="font-size: 1.5em; padding-top: 15px;"></i>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('backend.profile') }}"><i class="ti-user m-r-5"></i> Profile</a></li>
                    <li>
                        <a href="{{ route('backend-logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="ti-power-off m-r-5"></i> {{ __('Logout') }}</a>
                        <form id="logout-form" action="{{ route('backend-logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>