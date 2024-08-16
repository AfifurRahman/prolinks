<style>
    .highlighted {
        background-color: white;
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
                    <p>Users</p>
                    <div class="user-list">
                        <table>
                            <tbody>
                                <tr >
                                    <td><button data-toggle="collapse" data-target=".group1"><i class="fa fa-caret-down" style="font-size:16px"></i></a></button>
                                    <td class="group" style="cursor:pointer" onclick="setGroup(this)"><image class="group-icon" src="{{ url('template/images/icon_menu/group.png') }}">PT Tumbuh Makmur</td>
                                </tr>
                                <tr class="collapse in group1" style="cursor:pointer" onclick="setUser(this)" aria-expanded="true">
                                    <td></td>
                                    <td>
                                        Geraldine Abel
                                        <br>Client
                                    </td>
                                </tr>
                                <tr class="group">
                                    <td><button data-toggle="collapse" data-target=".group2"><i class="fa fa-caret-down" style="font-size:16px"></i></button></td>
                                    <td class="group" style="cursor:pointer" onclick="setGroup(this)"><img class="group-icon" src="{{ url('template/images/icon_menu/group.png') }}">PT Tumbuh Owi</td>
                                </tr>
                                <tr class="collapse in group2" style="cursor:pointer" onclick="setUser(this)" aria-expanded="true">
                                    <td></td>
                                    <td>
                                        Geraldine Anya
                                        <br>Administrator
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="permission-files-list">
                
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
    function setUser(rowElement) {
        const allRows = document.querySelectorAll('.collapse');
        allRows.forEach(row => row.classList.remove('highlighted'));

        const allGroup = document.querySelectorAll('.group');
        allGroup.forEach(group => group.classList.remove('highlighted'));
        
        rowElement.classList.add('highlighted');
    }

    function setGroup(rowElement) {
        const allRows = document.querySelectorAll('.collapse');
        allRows.forEach(row => row.classList.remove('highlighted'));

        const allGroup = document.querySelectorAll('.group');
        allGroup.forEach(group => group.classList.remove('highlighted'));
        
        rowElement.classList.add('highlighted');
    }
</script>
@endpush