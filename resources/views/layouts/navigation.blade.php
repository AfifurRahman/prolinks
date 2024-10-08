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
        
    </div>
</div>