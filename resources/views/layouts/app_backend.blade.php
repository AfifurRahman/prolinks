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
    <style type="text/css">
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
    </style>
    <script src="{{ url('template/js/modernizr.min.js') }}"></script>
</head>
<body class="fixed-left">
    <div id="wrapper">
        <div class="topbar">
            <div class="topbar-left" style="background: #F1F5F9;">
                <a href="{{ route('backend.dashboard') }}">
                    <div id="view_primary_logo" style="display: block;">
                        <img src="{{ url('template/images/logo2.png') }}" width="80%" style="margin-top: 10px;">
                    </div>
                    <div id="view_fav_logo" style="display: none;">
                        <img src="{{ url('template/images/logo_fav.png') }}" width="90%">
                    </div>
                </a>
            </div>
            <div class="navbar navbar-default" role="navigation">
                <div class="container">
                    <ul class="nav navbar-nav navbar-left">
                        <li style="margin-left: -35px;">
                            <button class="button-menu-mobile open-left waves-effect">
                                <img src="{{ url('template/images/icon-expand.png') }}" style="margin-top: 20px;" width="30" height="30">
                                <!-- <i class="fa fa-chevron-circle-left" style="color: #666;"></i> -->
                            </button>
                        </li>
                        <li style="margin-top: 10px;">
                            <h2 id="title" style="color:black">Dashboard</h2>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <div style="margin-top: 13px;">
                            @yield('navigations')
                        </div>
                    </ul>
                </div>
            </div>
        </div>
        
        @include('layouts.navigation')
        
        <div class="content-page">
            <div class="content">
                <div class="container">
                    <div style="padding: 20px; margin-top: 10px;">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
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

    <script type="text/javascript">
        $("input[data-type='currency']").on({
            keyup: function() {
              formatCurrency($(this));
            },
            blur: function() { 
              formatCurrency($(this), "blur");
            }
        });

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