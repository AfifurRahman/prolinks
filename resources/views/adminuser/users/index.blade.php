@extends('layouts.app_client')

<link href="{{ url('clientuser/index.css') }}" rel="stylesheet" type="text/css" />

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
                    
                    <div class="company_id">
                        <h5 class="usercompany">Company</h5>
                        <select class="form-control select2" name="company">
                            @foreach($companies as $company)
                                @if($company == 0)
                                    <option value="0">Unassigned</option>
                                @else
                                    <option value="{{$company}}">{{ DB::table('companies')->where('company_id', $company)->value('company_name') }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="formbutton">
                        <a class="cancelbtn" onclick="document.getElementById('inviteuser_form').style.display='none'">Cancel</a>
                        <button class="createbtn" type="submit">Invite</button>
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
                    <h5 class="modaltitle-text">Move to company</h5>
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
                        @foreach($companies as $company)
                            @if($company == 0)
                                <option value="0">Unassigned</option>
                            @else
                                <option value="{{$company}}">{{ DB::table('companies')->where('company_id', $company)->value('company_name') }}</option>
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
                        <tr>
                            <td>
                                <input type="checkbox" id="checkbox"/>
                            </td>
                            <td>
                                <image id="usericon" src="{{ url('template/images/Avatar.png') }}"></image>
                                {{ $user->email_address }}
                            </td>
                            <td>
                                {{ DB::table('companies')->where('company_id', $user->group_id)->value('company_name') }}
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
                                        <li><a onclick="moveGroup('{{ base64_encode($user->email_address) }}')">Move to company</a></li>
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