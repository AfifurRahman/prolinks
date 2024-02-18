@extends('layouts.app_backend')

@section('content')
    <style type="text/css">
        #tableRole td {
            vertical-align: middle;
        }
    </style>
    <div class="card-box">
        <a href="{{ route('backend.access-users.add-role') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Role
        </a>
        <a href="{{ route('backend.access-users.role') }}" data-toggle="tooltip" title="reload page" class="btn btn-success"><i class="fa fa-refresh"></i></a><br><br>
    	<table id="tableRole" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Role Name</th>
                    <th>Access</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                @if(count($role) > 0)
                    @foreach($role as $values)
                        <tr>
                        	<td>{{ $loop->iteration }}</td>
                        	<td>{{ $values->role_name }}</td>
                        	<td width="800">
                                @php
                                    $decodeAccess = json_decode($values->access, TRUE)
                                @endphp
                                @foreach($decodeAccess as $access)
                                    <label class="label label-success">{{ $access }}</label>
                                @endforeach   
                            </td>
                        	<td>
                        		<a href="{{ route('backend.access-users.edit-role', $values->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="edit Role"><i class="fa fa-edit"></i></a>
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
            $('#tableRole').dataTable();
        });
    </script>
@endpush