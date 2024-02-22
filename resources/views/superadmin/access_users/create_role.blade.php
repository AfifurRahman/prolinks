@extends('layouts.app_backend')

@section('content')
    <script type="text/javascript">
        var title = document.getElementById('title');
        @if(!empty($role->id))
            title.textContent = "Edit Role";
        @else
            title.textContent = "Add Role";
        @endif
    </script>
    @php
        $decodeAccess = [];
        if(!empty($role->access)){
            $decodeAccess = json_decode($role->access, TRUE);
        }
    @endphp
    <link rel="stylesheet" href="{{ url('backend/plugins/switchery/switchery.min.css') }}">
    <style type="text/css">
        .button-submit {
            position: fixed; 
            bottom: 0; 
            right: 0; 
            background-color: #FFFFFF; 
            box-shadow: 0px 0px 1px rgba(0, 0, 0, 0.32), 0px 4px 18px rgba(0, 0, 0, 0.12); 
            width: 100%; 
            height:auto; 
            padding: 15px;
            padding-right: 50px;
        }

        .button-submit button {
            margin-right: 5px;
        }

        .button-submit a {
            margin-right: 5px;
        }

        .box-image {
            border: dashed 1px #CCCCCC;
            width: auto;
            height : 300px;
            padding: 20px;
            border-radius: 10px;
        }

        .bg-default {
            background-color: #F5F5F5;
        }
    </style>

    <form action="{{ route('backend.access-users.save-role') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Role Name<span class="text-danger">*</span></label>
            <input type="hidden" name="id" value="{{ !empty($role->id) ? $role->id : NULL }}">
            <input required type="text" name="role_name" id="role_name" value="{{ !empty($role->role_name) ? $role->role_name : '' }}" class="form-control">
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Menu</th>
                    <th width="150">Access</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>Dashboard</b></th>
                    <td>
                        <input type="checkbox" id="dashboard" {{ in_array('dashboard', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="dashboard" />
                        <label for="dashboard" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <th colspan="2" class="bg-default">Pricing</th>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-list-alt"></i> View Pricing</td>
                    <td>
                        <input type="checkbox" id="list-pricing" {{ in_array('list-pricing', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="list-pricing" />
                        <label for="list-pricing" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-plus"></i> Add Pricing</td>
                    <td>
                        <input type="checkbox" id="add-pricing" {{ in_array('add-pricing', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="add-pricing" />
                        <label for="add-pricing" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-edit"></i> Edit Pricing</td>
                    <td>
                        <input type="checkbox" id="edit-pricing" {{ in_array('edit-pricing', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="edit-pricing" />
                        <label for="edit-pricing" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-trash"></i> Delete Pricing</td>
                    <td>
                        <input type="checkbox" id="delete-pricing" {{ in_array('delete-pricing', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="delete-pricing" />
                        <label for="delete-pricing" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <th colspan="2" class="bg-default">Client</th>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-list-alt"></i> View Client</td>
                    <td>
                        <input type="checkbox" id="list-client" {{ in_array('list-client', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="list-client" />
                        <label for="list-client" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-plus"></i> Add Client</td>
                    <td>
                        <input type="checkbox" id="add-client" {{ in_array('add-client', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="add-client" />
                        <label for="add-client" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-edit"></i> Edit Client</td>
                    <td>
                        <input type="checkbox" id="edit-client" {{ in_array('edit-client', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="edit-client" />
                        <label for="edit-client" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-trash"></i> Delete Client</td>
                    <td>
                        <input type="checkbox" id="delete-client" {{ in_array('delete-client', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="delete-client" />
                        <label for="delete-client" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-envelope"></i> Send Email Client</td>
                    <td>
                        <input type="checkbox" id="send-email-client" {{ in_array('send-email-client', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="send-email-client" />
                        <label for="send-email-client" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                
                <tr>
                    <th colspan="2" class="bg-default">Monitoring</th>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-tv"></i> View Monitoring</td>
                    <td>
                        <input type="checkbox" id="list-monitoring" {{ in_array('list-monitoring', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="list-monitoring" />
                        <label for="list-monitoring" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-desktop"></i> Detail Monitoring</td>
                    <td>
                        <input type="checkbox" id="detail-monitoring" {{ in_array('detail-monitoring', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="detail-monitoring" />
                        <label for="detail-monitoring" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <th colspan="2" class="bg-default">Role</th>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-list-alt"></i> View Role</td>
                    <td>
                        <input type="checkbox" id="list-role" {{ in_array('list-role', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="list-role" />
                        <label for="list-role" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-plus"></i> Add Role</td>
                    <td>
                        <input type="checkbox" id="add-role" {{ in_array('add-role', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="add-role" />
                        <label for="add-role" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-edit"></i> Edit Role</td>
                    <td>
                        <input type="checkbox" id="edit-role" {{ in_array('edit-role', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="edit-role" />
                        <label for="edit-role" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-trash"></i> Delete Role</td>
                    <td>
                        <input type="checkbox" id="delete-role" {{ in_array('delete-role', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="delete-role" />
                        <label for="delete-role" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <th colspan="2" class="bg-default">Admin Management</th>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-list-alt"></i> View Users</td>
                    <td>
                        <input type="checkbox" id="list-users" {{ in_array('list-users', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="list-users" />
                        <label for="list-users" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-plus"></i> Add Users</td>
                    <td>
                        <input type="checkbox" id="add-users" {{ in_array('add-users', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="add-users" />
                        <label for="add-users" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-edit"></i> Edit Users</td>
                    <td>
                        <input type="checkbox" id="edit-users" {{ in_array('edit-users', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="edit-users" />
                        <label for="edit-users" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
                <tr>
                    <td style="text-indent: 30px;"><i class="fa fa-trash"></i> Delete Users</td>
                    <td>
                        <input type="checkbox" id="delete-users" {{ in_array('delete-users', $decodeAccess) ? 'checked':'' }} switch="primary" name="menu_access[]" value="delete-users" />
                        <label for="delete-users" data-on-label="On" data-off-label="Off"></label>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="button-submit">
            <button type="submit" class="btn btn-primary btn-lg btn-rounded pull-right">
                <i class="fa fa-check"></i> Submit
            </button>
            <a href="{{ route('backend.access-users.role') }}" class="btn btn-lg btn-rounded btn-default pull-right">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </form>
@stop

@push('scripts')
    <script src="{{ url('backend/plugins/switchery/switchery.min.js') }}"></script>
    <script type="text/javascript">
        
    </script>
@endpush