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
        font-size:14.3px;
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

    #groupname{
        width:30%;
    }

    #role{
        width:25%;
    }

    #lastsigned{
        width:20%;
    }

    #status{
        width:18%;
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

    #emptybox {
        margin-left:26px;
    }

    #box_helper{
        margin-bottom:22px;
        display:flex;
        width:100%;
        justify-content: space-between;
    }

    #filter_button{
        padding:7px 15px 6px 17px;
        background: #FFFFFF;
        color:#546474;
        border:none;
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

    #modal-close-ico{
        width:24px;
        height:24px;
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
        border-radius:0px;
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
        justify-content: space-between;
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

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        padding: 12px 16px;
        z-index: 1;
        left:-120px;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

</style>

@section('navigationbar')
    <button id="create_group" onclick="document.getElementById('create-group').style.display='block'">Create Group</button>
    <button id="invite_user" onclick="document.getElementById('inviteuser_form').style.display='block'"><image id="addimg" src="{{ url('template/images/icon_menu/add.png') }}"></image>Invite User</button>
@endsection

@section('content')
    <script>
        var title = document.getElementById('title');
        title.textContent = 'Users';
    </script>

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
                    <h5>Invite users</h5>
                </div>
                
                <button class="modal-close" onclick="document.getElementById('inviteuser_form').style.display='none'">
                    <image id="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            
            <div class="modal-body">
                <form action="{{ route('adminuser.access-users.create')}}" method="POST">
                    @csrf
                    <label for="email_address">Email address</label><br>
                    <input type="email" name="email_address" id="email_address" placeholder="Enter email address, seperate by comma">
                    <br>
                    <br>
                    <label>
                        <input type="radio" name="role" value="0">Administrator
                        <p>Have full access to manage documents, QnA contents, invite users and access to all reports.</p>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="role" value="1">Collabolator
                        <p>Can view, upload, download, and ask questions based on their group permissions.</p>
                    </label><br>
                    <button>Cancel</button>
                    <button type="submit">Invite</button>
                </form>
            </div>
        </div>      
    </div>

    <div id="create-group" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div id="inviteuser-title">
                    <image id="inviteuser-ico" src="{{ url('template/images/icon_menu/invite_user.png') }}"></image>
                    <h5>Create Group</h5>
                </div>
                
                <button class="modal-close" onclick="document.getElementById('create-group').style.display='none'">
                    <image id="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            
            <div class="modal-body">
                <form action="{{ route('adminuser.access-users.create-group')}}" method="POST">
                    @csrf
                    <label for="group_name">Group name</label><br>
                    <input type="text" name="group_name" placeholder="Enter group name">
                    <br>
                    <label for="group_description">Group description (optional)</label><br>
                    <input type="text" name="group_description" placeholder="Enter group description">
                    <br>
                    <button>Cancel</button>
                    <button type="submit">Create</button>
                </form>
            </div>
        </div>      
    </div>

    <div id="moveuser" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div id="inviteuser-title">
                    <image id="inviteuser-ico" src="{{ url('template/images/icon_menu/invite_user.png') }}"></image>
                    <h5>Move to group - Under Construction</h5>
                </div>
                
                <button class="modal-close" onclick="document.getElementById('moveuser').style.display='none'">
                    <image id="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            
            <div class="modal-body">
                <form action="{{ route('adminuser.access-users.move-group')}}" method="POST">
                    @csrf
                    <label for="emails">User Email</label><br>
                    <input type="text" name="username" placeholder="Enter name">
                    <br>
                    <label for="group_name">Group Number</label><br>
                    <input type="text" name="group_num" placeholder="Enter group num">
                    <br>
                    <button>Cancel</button>
                    <button type="submit">Move</button>
                </form>
            </div>
        </div>      
    </div>


    <div id="table">
        <table id="tableUser">
            <thead>
                <tr>
                    <th id="check"><input type="checkbox" id="checkbox" disabled/></th>
                    <th id="groupname">Group / Name</th>
                    <th id="role">Role / Email</th>
                    <th id="lastsigned">Last signed in</th>
                    <th id="status">&ensp;Status</th>
                    <th id="navigationdot">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @if(count($clientuser) > 0)
                    @foreach($groupid as $groupId)
                        <tr>
                            <td>
                                <input type="checkbox" id="checkbox"/>
                            </td>
                            <td>
                                <image id="downarrow" onclick="toggleGroup('group{{$groupId}}')"src="{{ url('template/images/icon_menu/downarrow.png') }}"></image>
                                <image id="usericon" src="{{ url('template/images/icon_menu/group.png') }}"></image>
                                @if( $groupId == 0)
                                    Unassigned
                                @else
                                    {{ $groupnames[$groupId-1] }}
                                @endif
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <button class="button_ico">
                                    <image src="{{ url('template/images/icon_menu/button_ico.png') }}"></image>
                                </button>
                            </td>
                        </tr>

                        @foreach($clientuser->where('group_id', $groupId) as $user)
                            <tr class="group{{$groupId}}">
                                <td>
                                    <input type="checkbox" id="checkbox"/>
                                </td>
                                <td>
                                    <span id="emptybox"></span>
                                    <image id="usericon" src="{{ url('template/images/Avatar.png') }}"></image>
                                    {{ $user->email_address }}
                                </td>
                                <td>
                                    @if($user->role == 0) 
                                        Administrator
                                    @elseif($user->role == 1)
                                        Collaborator
                                    @endif
                                </td>
                                <td>
                                    @if(is_null($user->last_login))
                                        -
                                    @else
                                        {{ date('d M Y, H:i', strtotime($user->last_login)) }}
                                    @endif
                                </td>
                                <td>
                                    @if($user->status == 0)
                                        <span class="invited_status">Invited</span>
                                    @elseif($user->status == 1)
                                        <span class="active_status">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="button_ico">
                                            <img src="{{ url('template/images/icon_menu/button_ico.png') }}" alt="Dropdown Button">
                                        </button>
                                        <div class="dropdown-content">
                                            <a onclick="document.getElementById('moveuser').style.display='block'">Move to group</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script>
       function toggleGroup(groupName) {
            const rows = document.querySelectorAll('.' + groupName);
            rows.forEach(row => {
                row.classList.toggle('hidden');
            });
        };

		$(document).ready(function () {
            $('#tableUser').dataTable({
                "bPaginate": false,
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
    </script>
    @endpush
@endsection