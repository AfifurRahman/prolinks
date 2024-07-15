<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Prolinks | Login</title>
        <link rel="icon" type="image/x-icon" href="{{ url('template/images/favicon.png') }}">
        <link href="{{ url('template/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('template/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <style type="text/css">
            body {
                width: 100%;
                overflow: hidden;
            }

            .card {
                width: 60%;
                margin: 0 auto;
                margin-top: 50px;
                margin-left:50px;
            }

            .card img {
                width: 200px;
                height: 50px;
            }

            .card h3 {
                font-size: 28px;
            }

            .copyright {
                margin-top: 20px;
            }

            .copyright p {
                color: #999;
                font-size: 11px;
            }

            .copyright a {
                color: #999;
            }

            .input-group-addon {
                cursor: pointer;
            }
        </style>
    </head>
        <body>
            <div class="row">
                <div class="col-md-6">
                    <img src="{{ url('template/images/banner_login.png') }}" width="100%">
                </div>
                <div class="col-md-6">
                    <div class="card">
                        @if(Session::has('message'))
                            <div class="alert alert-warning">{{ Session::get('message') }}</div>
                        @endif
                        <img src="{{ url('template/images/logo2.png') }}" width="100%">
                        <h3>Welcome Back !</h3><br>
                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="email">{{ __('Email Address') }}</label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong style="color: red;">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>


                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" style="width: 100%;margin-top:18px;">
                                        Sign in
                                    </button>
                                    <hr>
                                   
                                    <div class="copyright">
                                        <p>
                                            Copyright ©{{ date('Y') }} Prolinks&nbsp;&nbsp; • <a href="">Privacy policy</a>&nbsp;&nbsp; • <a href="">Terms of use</a>
                                        </p>
                                    </div>
                                    <img src="{{ url('template/images/icon-iso.png') }}" style="width:100px; height: 30px;">&nbsp;&nbsp;
                                    <img src="{{ url('template/images/logo-pse.png') }}" style="width:100px; height: 40px;">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script src="{{ url('template/js/jquery-3.6.0.min.js') }}"></script>
            <script type="text/javascript">
                function showPswd() {
                    var pswd = document.getElementById("password");
                      if (pswd.type === "password") {
                        pswd.type = "text";
                        $("#icon-eye-slash").css("display", "none");
                        $("#icon-eye").css("display", "block");
                      } else {
                        pswd.type = "password";
                        $("#icon-eye-slash").css("display", "block");
                        $("#icon-eye").css("display", "none");
                      }
                }
            </script>
        </body>
</html>

