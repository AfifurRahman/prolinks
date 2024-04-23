<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Prolinks | Create Password</title>
        <link rel="icon" type="image/x-icon" href="{{ url('template/images/favicon.png') }}">
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
                height: 70px;
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
                        <h3>Create Password</h3><br>
                        <div class="card-body">
                            <form method="POST" action="{{ route('create-new-password', [$token, $email]) }}">
                                @csrf
                                <div class="form-group">
                                    <label>Fullname</label>
                                    <input id="fullname" type="text" class="form-control @error('fullname') is-invalid @enderror" name="fullname" required autocomplete="your fullname">
                                </div>
                                <div class="form-group">
                                    <label for="password">{{ __('Password') }}</label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="your password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong style="color: red;">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Confirm Password</label>
                                    <input id="confirm_password" type="password" class="form-control @error('confirm_password') is-invalid @enderror" name="confirm_password" required autocomplete="current-password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong style="color: red;">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                                        Create Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </body>
</html>