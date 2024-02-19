<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Prolinks | Login</title>
        <link href="{{ url('template/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <style type="text/css">
            body {
                width: 100%;
                overflow: hidden;
            }

            .card {
                width: 60%;
                margin: 0 auto;
                margin-top: 50px;
            }

            .card img {
                width: 200px;
                height: 50px;
            }

            .card h3 {
                font-size: 28px;
            }
        </style>
    </head>
        <body>
            <div class="row">
                <div class="col-md-6">
                    <img src="{{ url('template/images/banner_login.png') }}">
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
                                    <label for="password">{{ __('Password') }}</label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong style="color: red;">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                                        Sign in
                                    </button>
                                    <hr>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}">
                                            {{ __('Forgot Password?') }}
                                        </a>
                                    @endif
                                    <div class="copyright">
                                        <p>
                                            Copyright ©2024 Prolink&nbsp;&nbsp; • Privacy policy&nbsp;&nbsp; • Terms of use
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </body>
</html>

