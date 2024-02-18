@extends('layouts.app_backend')

@section('content')
    <style type="text/css">
        #tableAdminManagement td {
            vertical-align: middle;
        }
    </style>
    <div class="card-box">
        <a href="{{ route('backend.access-users.add-admin') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Admin
        </a>
        <a href="{{ route('backend.access-users.admin-management') }}" data-toggle="tooltip" title="reload page" class="btn btn-success"><i class="fa fa-refresh"></i></a><br><br>
        <table id="tableAdminManagement" class="table table-bordered table-hover">
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
                            <td><label class="label label-inverse">{{ !empty($backend->RefRole->role_name) ? $backend->RefRole->role_name : '' }}</label></td>
                            <td>
                                <a href="{{ route('backend.access-users.edit-admin', $backend->superuser_id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="edit admin management"><i class="fa fa-edit"></i></a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
@stop

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tableAdminManagement').dataTable();
        });
    </script>
@endpush