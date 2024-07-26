<style>
    .borderless td, .borderless th {
        border: none !important;
    }
</style>
{!! \globals::get_user_avatar_big(Auth::user()->name, Auth::user()->avatar_color) !!}
<h3>Edit Account Details</h3>
<form action="{{ route('adminuser.access-users.selfedit') }}" method="POST">
    @csrf
    <table class="table borderless">
        <tr>
            <th width="200">
                <p>Full name</p>
            </th>
            <td>
                <input type="text" name="name" class="edit_profile_box" value="{{ is_null(DB::table('client_users')->where('user_id', Auth::user()->user_id)->value('name')) ? "-" : Auth::user()->name }}"></input>
            </td>
        </tr>
        <tr>
            <th width="200">
                <p>Email</p>
            </th>
            <td>
                <input type="text" class="edit_profile_box" value="{{ is_null(Auth::user()->email) ? "-" : Auth::user()->email }}" disabled></input>
            </td>
        </tr>
        <tr>
            <th width="200">
                <p>Phone number</p>
            </th>
            <td>
                <input type="tel" name="phone"class="edit_profile_box" value="{{ is_null(Auth::user()->phone) ? "-" : Auth::user()->phone }}"></input>
            </td>
        </tr>
        <tr>
            <th width="200">
                <p>Company</p>
            </th>
            <td>
                <input type="text" class="edit_profile_box" value="{{ is_null(Auth::user()->client_id) ? "-" : DB::table('clients')->where('client_id', Auth::user()->client_id)->value('client_name')  }}" disabled></input>
            </td>
        </tr>
        <tr>
            <th width="200">
                <p>Job title</p>
            </th>
            <td>
                <input type="text" name="job_title" class="edit_profile_box" value="{{ is_null(Auth::user()->title) ? "-" : Auth::user()->title }}"></input>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fa fa-save"></i> Save</button>
                <a href="{{ route('setting', 'tab=account_setting') }}" class="btn btn-warning">Back</a>
            </td>
        </tr>
    </table>
</form>