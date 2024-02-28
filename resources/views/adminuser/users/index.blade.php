@extends('layouts.app_client')

<style>
    #button {
        position: relative;
        float:right;
        margin-bottom:15px;
    }
    #create_group {
        color:#0072EE;
        border:2px solid #F3F6F7;
        border-radius:9px;
        background:none;
        padding:9px 21px 8px 21px;
        margin-right:8px;
    }

    #invite_user {
        color:#FFFFFF;
        border:none;
        border-radius:9px;
        height:38px;
        background:#0072EE;
        padding:8px 19px 7px 14px;
    }

    #addimg{
        height:25px;
        width:26px;
        margin-top:-2px;
        margin-right:6px;
    }

    #tableUser{
        border-collapse: separate;
        border:1px solid #CED5DD;
        border-radius: 7px;
        width:100%;
    }

    #tableUser th {
        padding: 17px 0px 15px 10px;
        border-bottom: 1px solid #D0D5DD;
        background: #F9FAFB;
        font-size:15px;
        font-weight:600;
    }

    #tableUser td  {
        padding: 8px 0px 6px 10px;
        border-bottom:1px solid #CED5DD;
        font-size:13.5px;
        color:black;
    }

    #tableUser tbody tr:last-child td {
        border-bottom: none;
    }

    #check{
        width:3%;
    }

    #checkbox{
        margin-top:-2px;
        position: relative;
        width:30px;
        height:16px;
    }

    #name{
        width:25%;
    }

    #role{
        width:15%;
    }

    #company{
        width:20%;
    }
    #lastsigned{
        width:15%;
    }

    #status{
        width:10%;
    }

    #navigationdot{
        width:3%;
    }

    #downarrow{
        margin-top:-4px;
        margin-right:9px;
        width:13px;
        height:8px;
        cursor: pointer;
    }

    #usericon {
        margin-top:-4px;
        margin-right:4px;
        width:25px;
        height:25px;
    }

    #box_helper{
        margin-bottom:16px;
        display:flex;
        width:100%;
        justify-content: space-between;
    }

    #filter_button{
        padding:7px 15px 6px 17px;
        background: #FFFFFF; 
        color:#546474;
        border:1px solid #D0D5DD;
        border-radius:10px;
    }

    #filtericon{
        margin-top:-1px;
        margin-right:4px;
        height:23px;
        width:20px;
    }

    #searchbox{
        width:22%;
        padding:8px 10px 5px 12px;
        border:1px solid #CED5DD;
        border-radius: 8px;
    }

    #searchicon{
        width:19px;
        height:19px;
        margin-top:-3px;
        margin-right:4px;
    }

    #search_bar{
        border:none;
    }

    #inviteuser-title{
        display:flex;
    }

    #inviteuser-ico{
        height:22px;
        width:18px;
    }

    .modaltitle-text{
        margin-top:5px;
        margin-left:10px;
        font-size:15px;
        font-weight:600;
    }

    #modal-close-ico{
        margin-top:-6px;
        width:24px;
        height:24px;
    }

    #table {
        overflow:auto;
        height:70vh;
        max-height:70vh;
    }

    #group_name{
        width:100%;
        height:38px;
        border: 1px solid #aaa;
        border-radius:5px;
    }

    #group_description{
        width:100%;
        height:38px;
        border: 1px solid #aaa;
        border-radius:5px;
    }

    ::placeholder{
        color:#8C96A6;
    }

    .button_ico{
        border:none;
        background:transparent;
        margin-right:10px;

    }

    .dataTables_filter {
        display: none;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.65);
        
    }

    .modal-content {
        background-color: #fefefe;
        margin: 3% auto;
        border: 1px solid #888;
        width: 35%;
    }

    .modal-topbar {
        display:flex;
        border-bottom: 1px solid #D0D5DD;
        background: #F9FAFB;
        justify-content: space-between;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
        padding:10px 14px 0px 20px;
    }

    .modal-close {
        border:none;
        background:none;
    }

    .close:hover,
    .close:focus {
        cursor: pointer;
    }

    .invited_status{
        background: #EDF0F2;
        font-size:12px;
        font-weight:600;
        color: #1D2939;
        padding:5px 10px 5px 10px;
        border-radius:25px;
    }

    .active_status{
        background: #ECFDF3;
        font-size:12px;
        font-weight:600;
        color: #027A48; 
        padding:5px 10px 5px 10px;
        border-radius:25px;
    }

    .you_status{
        background: #D1E9FF;
        font-size:12px;
        font-weight:600;
        color: #175CD3; 
        padding:5px 10px 5px 10px;
        border-radius:25px;
    }

    .notificationlayer {
        position: absolute;
        width:100%;
        height:50px;
        z-index: 1;
        pointer-events: none;
    }

    #notification {
        background-color: #FFFFFF;
        border: 2px solid #12B76A;
        border-radius: 8px;
        display: flex;
        color: #232933;
        margin: 50px auto;
        text-align: center;
        height: 48px;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        transition: top 0.5s ease;    
    }

    #creategroup-ico {
        height:22px;
        width:22px;
        margin-right:-3px;
    }

    .notificationicon {
        width:20px;
        height:20px;
        margin-top:11px;
        margin-left:15px;
    }

    .notificationtext{
        margin-top:11px;
        margin-left:8px;
        margin-right:13px;
        font-size:14px;
    }

    .tags-default {
        width: 100%; /* Adjust this width as needed */
    }

    .tag-email{
        width:100%;
    }

    .userform{
        color:#1D2939;
        font-size:15px;
    }

    .userrole{
        color:#1D2939;
        font-size:15px;
        margin-top:24px;
    }

    .roleselect{
        display:flex;
        align-items: start;
    }

    .roletitle{
        font-weight:500;
        font-size:15px;
        color:#1D2939;
    }

    .roledetail{
        margin-left:10px;
    }

    .roledesc{
        margin-top:-12px;
    }

    .formbutton{
        width:100%;
        display: flex;
        justify-content: flex-end;
        margin-top:18px;
        margin-bottom:-14px;
    }

    .cancelbtn{
        padding: 11px 16px 9px 16px;
        border:none;
        background:none;
        color:#586474;
        margin-right:10px;
        cursor:pointer;
    }

    .createbtn{
        padding: 11px 16px 9px 16px;
        border:none;
        border-radius:6px;
        background:#1570EF;
        color:#FFFFFF;
    }

    .modal-body{
        margin:0px 4px 0px 4px;
    }
</style>



@section('navigationbar')
@endsection

@section('notification')
    @if(session('notification'))
        <div class="notificationlayer">
            <div class="notification" id="notification">
                <image class="notificationicon" src="{{ url('template/images/icon_menu/checklist.png') }}"></image>
                <p class="notificationtext">{{ session('notification') }}</p>
            </div>
        </div>
    @endif
@endsection

@section('content')
    <script>
        var title = document.getElementById('title');
        title.textContent = '';
    </script>

    
    <div id="box_helper">
        <h2 id="title" style="color:black;font-size:28px;">Users</h2>
        <button id="invite_user" onclick="document.getElementById('inviteuser_form').style.display='block'"><image id="addimg" src="{{ url('template/images/icon_menu/add.png') }}"></image>Invite User</button>
    </div>

    <div id="box_helper">
        <div>
            <button id="filter_button">
                <image id="filtericon" src="{{ url('template/images/icon_menu/filter.png') }}"></image>
                Filter
            </button>
        </div>
        <div id="searchbox">
            <image id="searchicon" src="{{ url('template/images/icon_menu/search.png') }}"></image>
            <input type="text" id="search_bar" placeholder="Search users...">
        </div>
    </div>

    <div id="inviteuser_form" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div id="inviteuser-title">
                    <image id="inviteuser-ico" src="{{ url('template/images/icon_menu/invite_user.png') }}"></image>
                    <h5 class="modaltitle-text">Invite users</h5>
                </div>
                
                <button class="modal-close" onclick="document.getElementById('inviteuser_form').style.display='none'">
                    <image id="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            
            <div class="modal-body">
                <form action="{{ route('adminuser.access-users.create')}}" method="POST">
                    @csrf
                    <h5 class="userform">Email address</h5>
                    <div class="tags-default tag-email">
                        <input name="email_address" id="email_address" placeholder="Enter email address" required/>
                    </div>
                    
                    <h5 class="userrole">Role</h5>
                    <div class="roleselect">
                        <input type="radio" name="role" value="0" required>
                        <div class="roledetail">
                            <p class="roletitle">Administrator<p>
                            <p class="roledesc">Have full access to manage documents, QnA contents, invite users and access to all reports.</p>
                        </div>
                    </div>

                    <div class="roleselect">
                        <input type="radio" name="role" value="1" required>
                        <div class="roledetail">
                            <p class="roletitle">Collaborator<p>
                            <p class="roledesc">Can view, upload, download, and ask questions based on their group permissions.</p>
                        </div>
                    </div>
                    
                    <div class="formbutton">
                        <a class="cancelbtn" onclick="document.getElementById('inviteuser_form').style.display='none'">Cancel</a>
                        <button class="createbtn" type="submit">Invite</button>
                    </div>
                </form>
            </div>
        </div>      
    </div>

    <div id="create-group" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div id="inviteuser-title">
                    <image id="creategroup-ico" src="{{ url('template/images/icon_menu/group.png') }}"></image>
                    <h5 class="modaltitle-text">Create Group</h5>
                </div>
                
                <button class="modal-close" onclick="document.getElementById('create-group').style.display='none'">
                    <image id="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            
            <div class="modal-body">
                <form action="{{ route('adminuser.access-users.create-group')}}" method="POST">
                    @csrf
                    <h5 class="userform">Group name</h5>
                    <input type="text" name="group_name" id="group_name" placeholder="Enter group name">
                    <br>
                    <h5 class="userrole">Group description (optional)</h5>
                    <input type="text" name="group_description" id="group_description" placeholder="Enter group description">
                    <h5 class="userrole">Invite users (optional)</h5>
                    <select class="select2 form-control select2-multiple" name="users" multiple="multiple" multiple data-placeholder="Choose ...">
                        @foreach($clientuser->where('group_id', 0) as $user)
                            <option value="{{$user->email_address}}">{{$user->email_address}}</option>
                        @endforeach
                    </select>
                    <br>
                    <br>
                    <div class="formbutton">
                        <a class="cancelbtn" onclick="document.getElementById('create-group').style.display='none'">Cancel</a>
                        <button class="createbtn" type="submit">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="moveuser" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div id="inviteuser-title">
                    <image id="creategroup-ico" src="{{ url('template/images/icon_menu/group.png') }}"></image>
                    <h5 class="modaltitle-text">Move to group</h5>
                </div>
                
                <button class="modal-close" onclick="document.getElementById('moveuser').style.display='none'">
                    <image id="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            
            <div class="modal-body">
                <form action="{{ route('adminuser.access-users.move-group')}}" method="POST">
                    @csrf
                    <input type="hidden" name="username" id="username">
                    <br>
                    <select class="form-control select2" name="group_num">
                        @foreach($groupid as $groupId)
                            @if($groupId == 0)
                                <option value="0">Unassigned</option>
                            @else
                                <option value="{{$groupId}}">{{ DB::table('client_user_groups')->where('id', $groupId)->value('group_name') }}</option>
                            @endif
                        @endforeach
                    </select>
                    <br>
                    <br>
                    <div class="formbutton">
                        <a class="cancelbtn" onclick="document.getElementById('moveuser').style.display='none'">Cancel</a>
                        <button type="submit" class="createbtn">Move</button>
                    </div>
                </form>
            </div>
        </div>      
    </div>


    <div id="table">
        <table id="tableUser">
            <thead>
                <tr>
                    <th id="check"><input type="checkbox" id="checkbox" disabled/></th>
                    <th id="name">Name</th>
                    <th id="company">Company</th>
                    <th id="role">Role</th>
                    <th id="status">&nbsp;Status</th>
                    <th id="lastsigned">Last signed in</th>
                    <th id="navigationdot">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            
                @if(count($owners) > 0)
                    @foreach($owners as $owner)
                        <tr class="company-group">
                            <td>
                                <input type="checkbox" id="checkbox"/>
                            </td>
                            <td>
                                <image id="usericon" src="{{ url('template/images/Avatar.png') }}"></image>
                                {{ $owner->email }}
                            </td>
                            <td>
                                {{ $owner->name }}
                            </td> 
                            <td>
                               Administrator
                            </td>
                            <td>
                                @if(Auth::user()->email == $owner->email)
                                    <span class="you_status">You</span>
                                @else
                                    <span class="active_status">Active</span>
                                @endif
                            </td>
                            <td>
                                @if(is_null($owner->last_signed))
                                    -
                                @else
                                    {{ date('d M Y, H:i', strtotime($owner->last_signed)) }}
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="button_ico dropdown-toggle" data-toggle="dropdown" disabled>
                                        <img src="{{ url('template/images/icon_menu/button_ico.png') }}" alt="Dropdown Button">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
                

                @if(count($clientuser) > 0)
                    @foreach($clientuser as $user)
                        <tr class="group{{$groupId}}">
                            <td>
                                <input type="checkbox" id="checkbox"/>
                            </td>
                            <td>
                                <image id="usericon" src="{{ url('template/images/Avatar.png') }}"></image>
                                {{ $user->email_address }}
                            </td>
                            <td>
                                {{ DB::table('client_user_groups')->where('id', $user->group_id)->value('group_name') }}
                            </td>
                            <td>
                                @if($user->role == 0) 
                                    Administrator
                                @elseif($user->role == 1)
                                    Collaborator
                                @endif
                            </td>
                            <td>
                                @if($user->email_address == Auth::User()->email)
                                    <span class="active_status">You</span>
                                @elseif($user->status == 1)
                                    <span class="active_status">Active</span>
                                @elseif($user->status == 0)
                                    <span class="invited_status">Invited</span>
                                @endif
                            </td>
                            <td>
                                @if(is_null(App\Models\User::where('email', $user->email_address)->first()->last_signed))
                                    -
                                @else
                                    {{ date('d M Y, H:i', strtotime(App\Models\User::where('email', $user->email_address)->first()->last_signed)) }}
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                        <img src="{{ url('template/images/icon_menu/button_ico.png') }}" alt="Dropdown Button">
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-top pull-right">
                                        <li><a onclick="moveGroup('{{ base64_encode($user->email_address) }}')">Move to group</a></li>
                                        <li><a>Make as owner</a></li>
                                        <li><a href="{{ route('adminuser.access-users.resend-email', base64_encode($user->email_address)) }}"></i> Send Email</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script>
        $('#email_address').tagsinput();

       function toggleGroup(groupName) {
            const rows = document.querySelectorAll('.' + groupName);
            rows.forEach(row => {
                row.classList.toggle('hidden');
            });
        };

		$(document).ready(function () {
            $('#tableUser').dataTable({
                "bPaginate": true,
                "bInfo": false,
                "bSort": false,
                "dom": 'rtip',
                "stripeClasses": false,
            });

            $('#search_bar').keyup(function() {
                var table = $('#tableUser').DataTable();
                table.search($(this).val()).draw();
            });
        });


        function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();

        function moveGroup(email) {
            document.getElementById('moveuser').style.display = 'block';
            document.getElementById('username').value = email;
        };
    </script>
    @endpush
@endsection