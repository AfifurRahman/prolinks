<style>
    .borderless td, .borderless th {
        border: none !important;
    }
</style>
    
<table class="table borderless">
    <tr>
        <th width="350">
            <p>Password</p>
            <p>Last changed {{ \Carbon\Carbon::parse(Auth::user()->updated_at)->format('d M Y, H:i') }}</p>
        </th>
        <td>
            <a href="">Change password</a>
        </td>
    </tr>
    <!--
    <tr>
        <th width="200">
            <p>2-step verification</p>
            <p>Protect your account with an extra layer of security during sign-in</p>
        </th>
        <td>
            <input type="checkbox" id="email-upload-file checked" switch="primary" name="is_upload_file" />
            <label for="email-upload-file" data-on-label="On" data-off-label="Off"></label>
        </td>
    </tr>
-->
</table>
