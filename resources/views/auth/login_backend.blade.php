<!DOCTYPE html>
<html>
    <head>
        <title>Prolinks | Backend Login</title>
        <link rel="icon" type="image/x-icon" href="{{ url('template/images/favicon.png') }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{ url('template/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('template/css/core.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('template/css/components.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('template/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('template/css/pages.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('template/css/menu.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('template/css/responsive.css') }}" rel="stylesheet" type="text/css" />

        <script src="{{ url('template/js/modernizr.min.js') }}"></script>
    </head>
    <body style="background-color: #F5F5F5;">
        <section>
            <div class="container-alt">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="wrapper-page">
                            <div align="center">
                                <img src="{{ url('template/images/logo2.png') }}" width="300" height="80">
                            </div>
                            <div class="m-t-20 account-pages">
                                <div class="text-center account-logo-box">
                                    <h2 class="text-uppercase">
                                        <a href="" class="text-success">
                                            
                                        </a>
                                    </h2>
                                </div>
                                @error('error')
                                    <div class="alert alert-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                                <div class="account-content" style="background-color: #FFFFFF; border-radius: 0 0 5px 5px;">
                                    <form class="form-horizontal" method="POST" action="{{ route('process-login-backend') }}">
                                        @csrf
                                        <div class="form-group ">
                                            <div class="col-xs-12">
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="email" autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <div class="col-xs-12">
                                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="password" required autocomplete="current-password">
                                            </div>
                                        </div>

                                        {!! NoCaptcha::display() !!}
                                        {!! NoCaptcha::renderJs() !!}
                                        
                                        @error('g-recaptcha-response')
                                            <span class="text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                        <br>
                                        <div class="row mb-0">
                                            <div class="col-md-8 offset-md-4">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-check"></i> {{ __('Login') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div><br>
                            <div class="text-center" style="font-size: 12px;">
                                &copy; {{ date('Y') }} Prolinks.id
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
