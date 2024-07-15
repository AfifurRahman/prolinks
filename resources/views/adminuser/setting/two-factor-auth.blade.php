<div>
        <h1>Two-Factor Authentication</h1>

        <div>
            <p>Scan the following QR code with your authenticator app:</p>
            <p>{!! $recoveryCodes !!}</p>
            <div>{!! $svg !!}</div>
        </div>


        <form method="POST" action="{{ route('adminuser.store.2fa') }}">
            @csrf

            <div>
                <label for="code">Code</label>
                <input id="code" type="text" name="code" required autofocus>
            </div>

            <div>
                <button type="submit">Enable Two-Factor Authentication</button>
            </div>
        </form>
    </div>