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
                margin-left: 50px;
            }

            .card img {
                width: 200px;
                height: 50px;
            }

            .card h3 {
                font-size: 28px;
            }

            .password-requirements {
                display:none;
                font-size:12px;
                margin-top:18px;
            }

            .password-requirements ul{
                list-style: none;
            }

            .confirm-password-msg{
                display:none;
                color:red;
                font-size:12px;
                margin-top:6px;
            }

            .valid {
                color: green;
            }

            .valid:before {
                position: relative;
                left: -20px;
                content: "✔";
            }

            .invalid {
                color: red;
            }

            .invalid:before {
                position: relative;
                left: -20px;
                content: "✖";
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
                    <h3>Create Password</h3><br>
                    <div class="card-body">
                        <form method="POST" action="{{ route('create-new-password', [$token, $email]) }}">
                            @csrf
                            <div class="form-group">
                                <label>Name</label>
                                <input id="fullname" type="text" class="form-control @error('fullname') is-invalid @enderror" name="fullname" required autocomplete="your fullname">
                            </div>
                            <div class="form-group">
                                <label for="password">{{ __('Password') }}</label>
                                <div class="input-group">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="your password">
                                    <span class="input-group-addon" style="background: transparent;" onclick="showPswd()"><i id="icon-eye-slash" class="fa fa-eye-slash"></i><i id="icon-eye" class="fa fa-eye" style="display: none;"></i></span>
                                </div>
                                <div class="password-requirements" id="password-requirements">
                                    <p style="font-weight:600;">Password must contain the following requirements:</p>
                                    <ul>
                                        <li id="lowercase" class="invalid">At least one lowercase letter</li>
                                        <li id="uppercase" class="invalid">At least one uppercase letter</li>
                                        <li id="special" class="invalid">At least one special character</li>
                                        <li id="number" class="invalid">At least one number</li>
                                        <li id="length" class="invalid">At least 8 characters</li>
                                    </ul>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong style="color: red;">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Confirm Password</label>
                                <div class="input-group">
                                    <input id="confirm_password" type="password" class="form-control @error('confirm_password') is-invalid @enderror" name="confirm_password" required autocomplete="current-password" disabled>
                                    <span class="input-group-addon" style="background: transparent;" onclick="showCPswd()"><i id="icon-eye-slash" class="fa fa-eye-slash"></i><i id="icon-eye" class="fa fa-eye" style="display: none;"></i></span>
                                </div>
                                <div class="confirm-password-msg" id="confirm-password-msg">
                                    <p>Please make sure your password match</p>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong style="color: red;">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" id="submit-button" class="btn btn-primary" style="width: 100%;" disabled>
                                    Create Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script>
        var submitButton = document.getElementById("submit-button");
        var passwordInput = document.getElementById("password");
        var confirmPasswordInput = document.getElementById("confirm_password");
        var lowercase = document.getElementById("lowercase");
        var uppercase = document.getElementById("uppercase");
        var special = document.getElementById("special");
        var number = document.getElementById("number");
        var length = document.getElementById("length");

        passwordInput.onfocus = function() {
            document.getElementById("password-requirements").style.display = "block";
        }

        passwordInput.onkeyup = function() {
            var lowerCaseLetters = /[a-z]/g;
            var upperCaseLetters = /[A-Z]/g;
            var numeric = /[0-9]/g;
            var specialLetters = /[\W_]/g;

            confirmPasswordInput.value = "";

            if (passwordInput.value.match(lowerCaseLetters)) {
                lowercase.classList.remove("invalid");
                lowercase.classList.add("valid");
            } else {
                lowercase.classList.remove("valid");
                lowercase.classList.add("invalid");
            }

            if (passwordInput.value.match(upperCaseLetters)) {
                uppercase.classList.remove("invalid");
                uppercase.classList.add("valid");
            } else {
                uppercase.classList.remove("valid");
                uppercase.classList.add("invalid");
            }

            if (passwordInput.value.match(specialLetters)) {
                special.classList.remove("invalid");
                special.classList.add("valid");
            } else {
                special.classList.remove("valid");
                special.classList.add("invalid");
            }

            if (passwordInput.value.match(numeric)) {
                number.classList.remove("invalid");
                number.classList.add("valid");
            } else {
                number.classList.remove("valid");
                number.classList.add("invalid");
            }

            if (passwordInput.value.length >= 8) {
                length.classList.remove("invalid");
                length.classList.add("valid");
            } else {
                length.classList.remove("valid");
                length.classList.add("invalid");
            }

            if (length.classList.contains('valid') && number.classList.contains('valid') && special.classList.contains('valid') && uppercase.classList.contains('valid') && lowercase.classList.contains('valid')) {
                confirmPasswordInput.disabled = false;
            } else {
                confirmPasswordInput.disabled = true;
            }
        }

        confirmPasswordInput.onkeyup = function() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (password === confirmPassword) {
                document.getElementById("confirm-password-msg").style.display = "none";
                submitButton.disabled = false;
            } else {
                document.getElementById("confirm-password-msg").style.display = "block";
                submitButton.disabled = true;
            }
        }

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

        function showCPswd() {
            var pswd = document.getElementById("confirm_password");
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
</html>