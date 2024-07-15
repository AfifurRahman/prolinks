@if(is_null(Auth::user()->two_factor_secret))
	<h2>Setting up 2-Factor authentication</h2>
	<p>Prepare your Authenticator App to use the 2-FA</p>
	<a href="{{ route('adminuser.enable.2fa') }}">Proceed</a>
@endif