<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Prolinks | Backend') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ url('template/images/favicon.png') }}">
    <link href="{{ url('template/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('template/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="{{ url('template/plugins/morris/morris.css') }}">

    <link href="{{ url('template/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('template/css/core.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('template/css/components.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('template/css/icons.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('template/css/pages.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('template/css/menu.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('template/css/responsive.css') }}" rel="stylesheet" type="text/css" />
    
    <link href="{{ url('template/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('template/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    
    <!--Inter font CSS -->
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    

    <!-- Tag Input CSS -->
    <link href="{{ url('template/plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <style type="text/css">
        .body{
            font-family: 'Inter';
        }
        .box-saldo {
            border: dashed 1px #CCC;
            padding: 10px;
        }

        .image-notif img{
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        .auth-client-name {
            display: inline-block;
        }

        .auth-client-name h5 {
            text-align: left;
        }

        .auth-client-name span {
            text-align: left;
            display: block;
            text-align: left;
            line-height: 5px;
        }

        .user-info{
            display:flex;
            width:260px;
            height:40px;
            padding:4px 8px 4px 8px;
            margin-right:-75px;
        }

        .user-img{
            width:40px;
            height:40px;
            radius:100px;
        }

        .user-profile{
            display:flex;
            cursor: pointer;
        }

        .user-detail{
            margin-left:6px;
        }

        .user-notification{
            width:20px;
            height:20px;
            margin:10px 20px 10px 12px;

        }
        .user-name{
            font-family: 'Inter';
            font-size:14px;
            font-weight:600;
            color:#1D2939;
            line-height:20px;
            white-space: nowrap;
        }

        .user-company{
            font-size:14px;
            color:#586474;
            line-height:0px;
            white-space: nowrap;
        }

        .user-avatar {
            background-color:#FDB022; 
            color:#FFF; 
            width:36px; 
            height:36px; 
            border-radius:100%; 
            text-align:center;
            font-size:25px; 
            font-weight:bold;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .user-avatar-small {
            background-color:#FDB022; 
            color:#FFF; 
            width:25px; 
            height:25px; 
            border-radius:100%; 
            text-align:center;
            font-size:17px; 
            font-weight:bold;
            display: inline-block;
        }

        .text-active {
            color: #1570EF !important;
            font-weight: bold !important;
        }

        .text-active img {
            filter: sepia(100%) hue-rotate(190deg) saturate(500%);
        }

        
    </style>
    <script src="{{ url('template/js/modernizr.min.js') }}"></script>
</head>
<body>
    <div id="wrapper">
        <div class="topbar">
            <div class="topbar-left" style="background: #F1F5F9;">
                <div id="view_primary_logo" style="display: block;">
                    <img src="{{ url('template/images/logo2.png') }}" class="logoprolink" width="80%" style="margin-top: 12px;">
                </div>
                <div id="view_fav_logo" style="display: none;">
                    <img src="{{ url('template/images/logo_fav.png') }}" width="90%">
                </div>
            </div>
            <div class="navbar navbar-default" role="navigation">
                <div class="container">
                    <ul class="nav navbar-nav navbar-left">
                        <li>
                            <button id="menuButton" class="button-menu-mobile open-left" style="margin-top: 24px">
                                <img src="{{ url('template/images/icon-expand.png') }}" width="26px" height="24px">
                            </button>
                        </li>
                    </ul>

                    <div>
                        @yield('notification')
                    </div>

                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="#" class="right-menu-item dropdown-toggle" data-toggle="dropdown">
                                <i class="mdi mdi-bell"></i>
                                <span class="badge up bg-success">
                                    @if(!\role::check_user_disabled())
                                        {{ count(\log::get_notification()) }}
                                    @else
                                        0
                                    @endif
                                </span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-right arrow-dropdown-menu arrow-menu-right dropdown-lg user-list notify-list">
                                <li>
                                    <h5>Notifications</h5>
                                </li>
                                @if(!\role::check_user_disabled())
                                    @if (count(\log::get_notification(5)))
                                        @foreach (\log::get_notification(5) as $notify )
                                            <li title="{{ $notify->sender_name }} - {{ $notify->text }}" data-toggle="tooltip" data-placement="left">
                                                <a href="#" data-url="{{ $notify->link }}" data-id="{{ $notify->id }}" onclick="readNotification(this)" class="user-list-item">
                                                    @if ($notify->type == 0)
                                                        <div class="icon bg-warning">
                                                            <i class="mdi mdi-comment"></i>
                                                        </div>
                                                    @elseif($notify->type == 1)
                                                        <div class="icon bg-info">
                                                            <i class="mdi mdi-file"></i>
                                                        </div>
                                                    @endif

                                                    <div class="user-desc">
                                                        <span class="name"><b>{{ $notify->sender_name }}</b> - {{ $notify->text }}</span>
                                                        <span class="time">{{ $notify->created_at }}</span>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                @endif
                                <li class="all-msgs text-center">
                                    <p class="m-0"><a href="{{ route('notification.list') }}">See all Notification</a></p>
                                </li>
                            </ul>

                        </li>
                        <li class="dropdown user-box">
                            <a href="" class="dropdown-toggle waves-effect user-link" data-toggle="dropdown" aria-expanded="true" style="display:flex;">
                                {!! \globals::get_user_avatar(Auth::user()->name, Auth::user()->avatar_color) !!}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right arrow-dropdown-menu arrow-menu-right user-list notify-list">
                                <li style="background-color:#f3f3f3;">
                                    <h5 style="margin-bottom:0em; padding: 10px 0px 0px 0px;">Hi, {{ Auth::user()->name }}</h5>
                                    <p style="text-align:center; margin-top:0em;">
                                        @if (Auth::user()->type == \globals::set_role_administrator())
                                            Administrator
                                        @elseif(Auth::user()->type == \globals::set_role_collaborator())
                                            Collaborator
                                        @elseif(Auth::user()->type == \globals::set_role_client())
                                            Reviewer
                                        @endif
                                    </p>
                                </li>
                                <li>
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <input type="checkbox" id="logout-checkbox" style="display: none;">
                                        <label for="logout-checkbox"><i class="ti-power-off m-r-5"></i>Sign out</label>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        @include('layouts.navigation_client')
        
        <div class="content-page">
            <div class="content">
                <div class="container">
                    <div class="card-boxxs" style="margin-top:6px;">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

        @include('widget_modal_project')
    </div>

    @include('sweetalert::alert')

    <!-- jQuery  -->
    <script src="{{ url('template/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ url('template/js/bootstrap.min.js') }}"></script>
    <script src="{{ url('template/js/detect.js') }}"></script>
    <script src="{{ url('template/js/fastclick.js') }}"></script>
    <script src="{{ url('template/js/jquery.blockUI.js') }}"></script>
    <script src="{{ url('template/js/waves.js') }}"></script>
    <script src="{{ url('template/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ url('template/js/jquery.scrollTo.min.js') }}"></script>
    <script src="{{ url('template/plugins/switchery/switchery.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ url('template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('template/plugins/datatables/dataTables.bootstrap.js') }}"></script>

    <script src="{{ url('template/pages/jquery.dashboard.js') }}"></script>

    <!-- App js -->
    <script src="{{ url('template/js/jquery.core.js') }}"></script>
    <script src="{{ url('template/js/jquery.app.js') }}"></script>

    <!--Morris Chart-->
    <script src="{{ url('template/plugins/morris/morris.min.js') }}"></script>
    <script src="{{ url('template/plugins/raphael/raphael-min.js') }}"></script>

    <script src="{{ url('template/js/jquery.dashboard.js') }}"></script>

    <!-- select2 -->
    <script src="{{ url('template/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('template/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>

    <!-- taginput -->
    <script src="{{ url('template/plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js') }}" type="text/javascript"></script>
    
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="{{ url('template/plugins/viima/css/jquery-comments.css') }}">
    <script type="text/javascript" src="{{ url('template/plugins/viima/js/jquery-comments.js') }}"></script>

    <script type="text/javascript">
        $("input[data-type='currency']").on({
            keyup: function() {
              formatCurrency($(this));
            },
            blur: function() { 
              formatCurrency($(this), "blur");
            }
        });

        $(".select2").select2();

        $("#main_project_id").change(function(){
            $("#app-change-project").submit();
        });

        document.addEventListener('DOMContentLoaded', function() {
            var menuButton = document.querySelector('.button-menu-mobile');
            var projectGroup = document.querySelector('.project-group');

            menuButton.addEventListener('click', function() {
                if (projectGroup.style.display === 'none') {
                    projectGroup.style.display = 'block';
                } else {
                    projectGroup.style.display = 'none';
                }
            });
        });

        function readNotification(element) {
            var id = $(element).data('id');
            var url = $(element).data('url');
            $.ajax({
                type: "POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                url: "{{ route('notification.read') }}",
                success:function(output) {
                    window.location.href = url;
                }
            });
        }

        function formatNumber(n) {
          // format number 1000000 to 1,234,567
          return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }

        function replaceFormatNumber(n) {
            return n.replace(/\./g, '');
        }

        function formatCurrency(input, blur) {
          // appends $ to value, validates decimal side
          // and puts cursor back in right position.
          
          // get input value
          var input_val = input.val();
          
          // don't validate empty input
          if (input_val === "") { return; }
          
          // original length
          var original_len = input_val.length;

          // initial caret position 
          var caret_pos = input.prop("selectionStart");
            
          // check for decimal
          if (input_val.indexOf(".") >= 0) {

            // get position of first decimal
            // this prevents multiple decimals from
            // being entered
            var decimal_pos = input_val.indexOf(".");

            // split number by decimal point
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            // add commas to left side of number
            left_side = formatNumber(left_side);

            // validate right side
            right_side = formatNumber(right_side);
            
            // On blur make sure 2 numbers after decimal
            
            // Limit decimal to only 2 digits
            right_side = right_side.substring(0, 2);

            // join number by .
            input_val = left_side + "." + right_side;

          } else {
            // no decimal entered
            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;
            
            // final formatting
            if (blur === "blur") {
              input_val += "";
            }
          }
          
          // send updated string to input
          input.val(input_val);

          // put caret back in the right position
          var updated_len = input_val.length;
          caret_pos = updated_len - original_len + caret_pos;
          input[0].setSelectionRange(caret_pos, caret_pos);
        }

        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function () {
            window.history.pushState(null, "", window.location.href);
        };
    </script>
    @stack('scripts')
</body>
</html>