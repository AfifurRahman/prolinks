@extends('layouts.app_backend')

@section('content')
     <script type="text/javascript">
        var title = document.getElementById('title');
        title.textContent = "Admin Management";
    </script>
    <style type="text/css">
        #tableAdminManagement td {
            vertical-align: middle;
        }

        #tableAdminManagement{
            border-collapse: separate;
            border:1px solid #F1F1F1;
            border-radius: 7px;
            width:100%
        }

        #tableAdminManagement th {
            padding: 15px 0px 15px 10px;
            border-bottom:1px solid #F1F1F1;
            font-size:14px;
            font-weight:600;
        }

        #tableAdminManagement td  {
            padding: 13px 0px 13px 10px;
            border-bottom:1px solid #F1F1F1;
            font-size:13.5px;
            color:black;
        }

        #tableAdminManagement tbody tr:last-child td{
            border-bottom: none;
        }

        #tableAdminManagement tbody tr:hover {
            background-color: #f0f0f0;
        }
    </style>
    <div class="pull-right">
        <a href="{{ route('backend.access-users.add-admin') }}" class="btn btn-primary btn-lg btn-rounded">
            <i class="fa fa-plus-circle"></i> Add Admin
        </a>
        <a href="{{ route('backend.access-users.admin-management') }}" data-toggle="tooltip" data-placement="bottom" title="reload page" class="btn btn-lg btn-rounded btn-success"><i class="fa fa-refresh"></i></a>
    </div><div style="clear:both;"></div> <br>
    <table id="tableAdminManagement">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>No HP</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if(count($admin) > 0)
                @foreach($admin as $backend)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $backend->first_name." ".$backend->last_name }}</td>
                        <td>{{ $backend->phone }}</td>
                        <td>{{ $backend->email }}</td>
                        <td><label class="label label-inverse" style="border-radius: 10px;">{{ !empty($backend->RefRole->role_name) ? $backend->RefRole->role_name : '' }}</label></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-md dropdown-toggle" type="button" data-toggle="dropdown" style="border: solid 0px; background: transparent;">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="{{ route('backend.access-users.edit-admin', $backend->superuser_id) }}"><i class="fa fa-edit"></i> Edit</a>
                                    </li>
                                    <li><a href="" onclick="return confirm('are you sure delete this item ?')"><i class="fa fa-trash"></i> Delete</a></li>
                                </ul>
                            </div>
                            
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
@stop

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tableAdminManagement').dataTable();
        });
    </script>
@endpush