<style>
    .user-avatar {
        background-color:#FDB022; 
        color:#FFF; 
        width:40px; 
        height:40px; 
        border-radius:100%; 
        text-align:center;
        font-size:27px; 
        font-weight:bold;
    }

    .user-avatar-small {
        background-color:#FDB022; 
        color:#FFF; 
        width:25px; 
        height:25px; 
        border-radius:100%; 
        text-align:center;
        font-size:17px; 
        font-weight:bold;
        display: inline-block;
    }
</style>
<table class="table">
    <thead>
        <tr>
            <th><input type="checkbox" id="checkbox" disabled/></th>
            <th>Name</th>
            <th>Group</th>
            <th>Role</th>
            <th>&nbsp;Status</th>
            <th>Last signed in</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @if(count($clientuser) > 0)
            @foreach($clientuser as $user)
                <tr>
                    <td>
                        <input type="checkbox" id="checkbox"/>
                    </td>
                    <td>
                        <a href="{{ route('adminuser.access-users.detail', $user->user_id) }}">
                            @if(!empty($user->RefUser->name) && $user->RefUser->name != "null")
                                {!! \globals::get_user_avatar_small($user->name, !empty($user->RefUser->avatar_color) ? $user->RefUser->avatar_color : '#000') !!}
                                {{ $user->name }}
                            @else
                                {!! \globals::get_user_avatar_small($user->email_address, !empty($user->RefUser->avatar_color) ? $user->RefUser->avatar_color : '#000') !!}
                                {{ $user->email_address }}
                            @endif
                        </a>
                    </td>
                    <td>
                        @if($user->role == 0)
                            <span class="you_status">All</span>
                        @else
                            @php $grups = DB::table('assign_user_group')->select('access_group.group_name')->join('access_group', 'assign_user_group.group_id', 'access_group.group_id')->where('assign_user_group.user_id', $user->user_id)->where('assign_user_group.client_id', $clients->client_id)->get() @endphp
                            @foreach($grups as $grup)
                                <span class="invited_status">{{ $grup->group_name }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if($user->role == 0) 
                            Administrator
                        @elseif($user->role == 1)
                            Collaborator
                        @elseif($user->role == 2)
                            Client
                        @endif
                    </td>
                    <td>
                        @if($user->status == 1)
                            <span class="active_status">Active</span>
                        @elseif($user->status == 2)
                            <span class="disabled_status">Disabled</span>
                        @elseif($user->status == 0)
                            <span class="invited_status">Invited</span>
                        @endif
                    </td>
                    <td>
                        @if(is_null(App\Models\User::where('email', $user->email_address)->value('last_signed')))
                            -
                        @else
                            {{ date('d M Y, H:i', strtotime(App\Models\User::where('email', $user->email_address)->value('last_signed'))) }}
                        @endif
                    </td>
                    <td></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>