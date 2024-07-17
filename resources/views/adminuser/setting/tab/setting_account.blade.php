<style>
    .borderless td, .borderless th {
        border: none !important;
    }
</style>
{!! \globals::get_user_avatar_big(Auth::user()->name, Auth::user()->avatar_color) !!}
<h3>Account details<a href="?tab=edit_account" style="margin-left:6px;"><img style="width:28px;"src="{{ url('template/images/icon_menu/edit_profile.png') }}"></a></h3>
    
<table class="table borderless">
    <tr>
        <th width="200">
            <p>Full name</p>
        </th>
        <td>
            <p>{{ is_null(Auth::user()->name) ? "-" : Auth::user()->name }}</p>
        </td>
    </tr>
    <tr>
        <th width="200">
            <p>Email</p>
        </th>
        <td>
            <p>{{ is_null(Auth::user()->email) ? "-" : Auth::user()->email }}</p>
        </td>
    </tr>
    <tr>
        <th width="200">
            <p>Phone number</p>
        </th>
        <td>
            <p>{{ is_null(Auth::user()->phone) ? "-" : Auth::user()->phone }}</p>
        </td>
    </tr>
    <tr>
        <th width="200">
            <p>Company</p>
        </th>
        <td>
            <p>{{ is_null(Auth::user()->client_id) ? "-" : DB::table('clients')->where('client_id', Auth::user()->client_id)->value('client_name')  }}</p>
        </td>
    </tr>
    <tr>
        <th width="200">
            <p>Job title</p>
        </th>
        <td>
            <p>{{ is_null(Auth::user()->title) ? "-" : Auth::user()->title }}</p>
        </td>
    </tr>
    <tr>
        <th width="200">
            <p>Two-factor authentication</p>
        </th>
        <td>
            <p>{{ is_null(Auth::user()->two_factor_confirmed_at) ? "Disabled" : "Enabled at ". \Carbon\Carbon::parse(Auth::user()->two_factor_confirmed_at)->format('d M Y, H:i:s') . " GMT+7" }}</p>
            @if(is_null(Auth::user()->two_factor_confirmed_at))
                <a href="?tab=2fa_setup">Set up Two-Factor Authentication</a>
            @endif
        </td>
    </tr>
</table>
