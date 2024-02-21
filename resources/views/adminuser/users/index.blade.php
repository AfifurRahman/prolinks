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
        padding:9px 19px 8px 16px;
    }

    #addimg{
        height:25px;
        width:27px;
        margin-top:-2px;
        margin-right:6px;
    }

    #tableUser{
        border-collapse: separate;
        border:1px solid #CED5DD;
        border-radius: 7px;
        width:100%
    }

    #tableUser th {
        padding: 15px 0px 15px 10px;
        border-bottom:1px solid #CED5DD;
        font-size:14px;
        font-weight:600;
    }

    #tableUser td  {
        padding: 13px 0px 13px 10px;
        border-bottom:1px solid #CED5DD;
        font-size:13.5px;
        color:black;
    }

    #tableUser tbody tr:last-child td{
        border-bottom: none;
    }

    #tableUser tbody tr:hover {
        background-color: #f0f0f0;
    }

    #check{
        width:3%;
    }

    #checkbox{
        margin-top:-2px;
        position: relative;
        width:30px;
        height:15px;
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
</style>

@section('navigationbar')
    <button id="create_group">Create Group</button>
    <button id="invite_user"><image id="addimg" src="{{ url('template/images/icon_menu/add.png') }}"></image>Invite User</button>
@endsection

@section('content')
    <script>
        var title = document.getElementById('title');
        title.textContent = 'Users';
    </script>

    <div id="filter_search">
        <!-- Filter button -->
        <button id="filter_button">Filter</button>
        <!-- Search bar -->
        <input type="text" id="search_bar" placeholder="Search...">
    </div>
    
    <div id="table">
        <table id="tableUser">
            <thead>
                <tr>
                    <th id="check"><input type="checkbox" id="checkbox" disabled/></th>
                    <th id="groupname">Group / Name</th>
                    <th id="role">Role / Email</th>
                    <th id="lastsigned">Last signed in</th>
                    <th id="status">Access to</th>
                    <th id="navigationdot">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox" id="checkbox"/></td>
                    <td>
                        <image id="downarrow" onclick="toggleGroup('group1')" src="{{ url('template/images/icon_menu/downarrow.png') }}"></image>
                        <image id="usericon" src="{{ url('template/images/icon_menu/group.png') }}"></image>
                        PT. Ayam Goreng
                    </td>
                    <td>Presiden</td>
                    <td>17 Agustus 1999</td>
                    <td>Aktif</td>
                    <td></td>
                </tr>   
                <tr class="group1">
                    <td><input type="checkbox" id="checkbox"/></td>
                    <td>
                        <span id="emptybox"></span>
                        <image id="usericon" src="{{ url('template/images/icon_menu/group.png') }}"></image>
                        Budi Budiman
                    </td>
                    <td>Presiden</td>
                    <td>17 Agustus 1999</td>
                    <td>Aktif</td>
                    <td></td>
                </tr>   
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
           	$('#tableUser').dataTable(
            );
        });
    </script>
    @endpush
@endsection