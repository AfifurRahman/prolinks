<div id="modal-add-permission" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header">
                <div class="custom-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="titleModal">
                        Permission Settings
                    </h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="permission-user-listx">
                            <h4>Users</h4>
                            <table id="permission-user-list-table" >
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($listusers) > 0) 
                                        @foreach($listusers->unique('group_id') as $group)
                                            @if(!empty(DB::table('access_group')->where('group_id', $group->group_id)->value('group_name')))
                                                <tr>
                                                    <td>
                                                        <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/group.png') }}" />
                                                    </td>
                                                    <td class="permission-user-list-td">
                                                        {{ DB::table('access_group')->where('group_id', $group->group_id)->value('group_name') }}
                                                    </td>
                                                </tr>
                                                @foreach($listusers as $user)
                                                    @if(!is_null(DB::table('assign_project')->where('project_id', explode('/', $origin)[2])->where('user_id', $user->user_id)->value('email')))
                                                        @if($user->group_id == $group->group_id)
                                                            <tr>
                                                                <td></td>
                                                                <td>
                                                                    <div style="cursor:pointer;">
                                                                        <a onclick="checkUserPermission('{{ $user->user_id }}')">
                                                                            <p class="permission-user-list-td">{{ $user->name }}</p>
                                                                            <p class="permission-user-list-td2">
                                                                            {{ $user->role == 0 ? 'Administrator' : ($user->role == 1 ? 'Collaborator' : 'Client') }}
                                                                            </p>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                        @foreach($listusers->unique('group_id') as $group)
                                            @if(empty(DB::table('access_group')->where('group_id', $group->group_id)->value('group_name')))
                                                @foreach($listusers as $user)
                                                    @if(!is_null(DB::table('assign_project')->where('project_id', explode('/', $origin)[2])->where('user_id', $user->user_id)->value('email')))
                                                        @if(empty($user->group_id))
                                                            <tr>
                                                                <td>
                                                                    <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/user.png') }}" />
                                                                    <br>
                                                                    &nbsp;
                                                                </td>
                                                                <td style="cursor:pointer;">
                                                                    <div style="cursor:pointer;">
                                                                        <a onclick="checkUserPermission('{{ $user->user_id }}')">
                                                                            <p class="permission-user-list-td">{{ $user->name }}</p>
                                                                            <p class="permission-user-list-td2">
                                                                            {{ $user->role == 0 ? 'Administrator' : ($user->role == 1 ? 'Collaborator' : 'Client') }}
                                                                            </p>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="permission-file-listx">
                            <h4 id="permissionUser">Select a user</h4>
                            <table id="permission-file-list-table" width="100%" class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>
                                            File Name
                                        </th>
                                        <th>
                                            <input type="checkbox" id="all_checkbox" class="setPermissionBox" style="width:30px; height:16px;"></input>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="fileList">
                                    @foreach($fileList as $file)
                                        @if( DB::table('upload_files')->where('basename', $file)->value('status') == 1 )
                                            <tr>
                                                <td>
                                                    {{DB::table('upload_files')->where('basename', $file)->value('name')}}
                                                </td>
                                                <td>
                                                    <input type="checkbox" id="{{$file}}" class="setPermissionBox" value="{{$file}}" onclick="handleChangeBox(this)" style="width:30px; height:16px;"></input>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">
                            <a class="cancel-btn" data-dismiss="modal">Cancel</a>
                            <button class="create-btn" id="setPermissionButton" onclick="savePermission()">Save settings</button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="IDuser" value="">
            </div>
        </div>
    </div>
</div>