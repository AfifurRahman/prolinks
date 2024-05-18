@extends('layouts.app_backend')

@section('content')
    <script type="text/javascript">
        var title = document.getElementById('title');
        title.textContent = "Role";
    </script>
    <style type="text/css">
        #tableRole td {
            vertical-align: middle;
        }

        #tableRole{
            border-collapse: separate;
            border:1px solid #F1F1F1;
            border-radius: 7px;
            width:100%
        }

        #tableRole th {
            padding: 15px 0px 15px 10px;
            border-bottom:1px solid #F1F1F1;
            font-size:14px;
            font-weight:600;
        }

        #tableRole td  {
            padding: 13px 0px 13px 10px;
            border-bottom:1px solid #F1F1F1;
            font-size:13.5px;
            color:black;
        }

        #tableRole tbody tr:last-child td{
            border-bottom: none;
        }

        #tableRole tbody tr:hover {
            background-color: #f0f0f0;
        }
    </style>
    <div class="pull-right">
        <a href="{{ route('backend.access-users.add-role') }}" class="btn btn-primary btn-lg btn-rounded">
            <i class="fa fa-plus-circle"></i> Add Role
        </a>
        <a href="{{ route('backend.access-users.role') }}" data-toggle="tooltip" data-placement="bottom" title="reload page" class="btn btn-success btn-rounded btn-lg"><i class="fa fa-refresh"></i></a>
    </div><div style="clear:both;"></div> <br>
    <table id="tableRole">
        <thead>
            <tr>
                <th width="50">#</th>
                <th>Role Name</th>
                <th>Access</th>
                <th>Action</th>
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
                                <label class="label label-success" style=" margin-bottom:5px; display: inline-block; padding: 7px;">{{ $access }}</label>
                            @endforeach   
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-md dropdown-toggle" type="button" data-toggle="dropdown" style="border: solid 0px; background: transparent;">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="{{ route('backend.access-users.edit-role', $values->id) }}"><i class="fa fa-edit"></i> Edit Role</a>
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
            $('#tableRole').dataTable();
        });
    </script>
@endpush