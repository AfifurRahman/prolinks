<style>
    .highlighted {
        background-color: white;
        color: #1570EF;
    }
    
    li {
        list-style-type:none;
    }

    .permission-body{
        display:flex;
    }

    .items{
        display:flex;
        justify-content:space-between;
        margin-left:-20px;
        max-width:100%;
    }

    .expand-btn {
        background:none;
        border:none;
    }

    .users-list-header {
        font-size:18px;
        font-weight:500;
        line-height:12px;
        color:#1D2939;
        margin-top:10px;
    }

    .user-list-table {
        width:100%;
    }

    .user-list-table td{
        padding-top:10px;
        padding-bottom:8px;
        border-radius:5px;
        margin:0px;
    }

    .table-icon {
        width:8%;
        padding-left:6px;
    }

    .group-name {
        color:#1D2939;
        font-size:14px;
        font-weight:400;
        line-height:0px;
    }

    .user-name {
        color:#1D2939;
        font-size:14px;
        font-weight:400;
        line-height:0px;
    }

</style>

<div id="set-permission-modal" class="modal">
    <div class="modal-content-permission">
        <div class="modal-topbar">
            <div class="upload-modal-title">
                <h5 class="modal-delete-file-title">Permission Settings</h5>
            </div>
            <button class="modal-close" onclick="document.getElementById('set-permission-modal').style.display='none'">
                <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
            </button>
        </div>
        <div class="permission-modal-content">
           <div class="permission-body">
                <div class="permission-users-list">
                    <p class="users-list-header">Users</p>
                    <div class="user-list">
                        <table class="user-list-table">
                            <tbody>
                                @foreach($listusers->unique('group_id') as $key=>$group)
                                    @if(!empty(DB::table('access_group')->where('group_id', $group->group_id)->value('group_name')))
                                        <tr class="group{{$key}}header">
                                            <td class="table-icon"><button class="expand-btn" data-toggle="collapse" data-target=".group{{$key}}"><i class="fa fa-caret-down" style="font-size:16px"></i></button>
                                            <td class="group" style="cursor:pointer" onclick="setGroup(this)"><image class="group-icon" src="{{ url('template/images/icon_menu/group.png') }}"><span class="group-name"> {{ DB::table('access_group')->where('group_id', $group->group_id)->value('group_name') }} </span></td>
                                        </tr>
                                        @foreach($listusers as $user)
                                                @if($user->group_id == $group->group_id)
                                                    <tr class="collapse in group{{$key}}" style="cursor:pointer" onclick="setUser(this)" aria-expanded="true">
                                                        <td></td>
                                                        <td>
                                                            <span class="user-name">{{ is_null($user->name) ? "Unnamed User" : $user->name }}</span>
                                                            <br>  {{ $user->role == 0 ? 'Administrator' : ($user->role == 1 ? 'Collaborator' : 'Client') }}
                                                        </td>
                                                    </tr>
                                                @endif
                                        @endforeach
                                    @endif
                                @endforeach
                                @foreach($listusers->unique('group_id') as $group)
                                    @if(empty(DB::table('access_group')->where('group_id', $group->group_id)->value('group_name')))
                                        @foreach($listusers as $user)
                                                @if(empty($user->group_id))
                                                    <tr class="ungrouped-users" style="cursor:pointer" onclick="setUser(this)" aria-expanded="true">
                                                        <td class="table-icon" style="vertical-align:top;"><image class="user-icon" src="{{ url('template/images/icon_menu/user.png') }}"></td>
                                                        <td><span class="user-name">{{ is_null($user->name) ? "Unnamed User" : $user->name }}</span>
                                                        <br>  {{ $user->role == 0 ? 'Administrator' : ($user->role == 1 ? 'Collaborator' : 'Client') }}
                                                        </td>
                                                    </tr>
                                                @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="permission-files-list">
                    <p>Files</p>
                    {!! \App\Http\Controllers\Adminuser\DocumentController::generateFileTree(storage_path('app/'.$origin)) !!}
                </div>
            </div>
            <div class="modal-body">
                <div class="permission-form-button">
                    <a onclick="document.getElementById('set-permission-modal').style.display='none'" class="cancel-btn">Cancel</a>
                    <button class="create-btn" id="setPermissionButton" onclick="savePermission()">Save settings</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setUser(element) {
        document.querySelectorAll('.highlighted').forEach(el => el.classList.remove('highlighted'));
        
        element.classList.add('highlighted');
    }

    function setGroup(element) {
        document.querySelectorAll('.highlighted').forEach(el => el.classList.remove('highlighted'));
        
        const button = element.tagName === 'BUTTON' ? element : element.closest('tr').querySelector('button');
        const targetClass = button ? button.getAttribute('data-target') : '';
        const targetRows = document.querySelectorAll(targetClass);
        targetRows.forEach(row => row.classList.add('highlighted'));

        const groupRow = element.closest('tr');
        groupRow.classList.add('highlighted');

        element.classList.add('highlighted');
    }
</script>
@endpush