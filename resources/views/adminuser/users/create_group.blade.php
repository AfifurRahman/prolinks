<div id="create_group_form" class="modal">
    <div class="modal-content">
        <div class="modal-topbar">
            <div id="inviteuser-title">
                <image id="inviteuser-ico" src="{{ url('template/images/icon_menu/group.png') }}"></image>
                <h5 class="modaltitle-text">Create Group</h5>
            </div>
            
            <button class="modal-close" onclick="document.getElementById('create_group_form').style.display='none'">
                <image id="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
            </button>
        </div>
        
        <div class="modal-body">
            <form action="{{ route('adminuser.access-users.create-group') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Group name</label>
                    <input name="group_name" id="group_name" placeholder="Enter group name" class="form-control" required/>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="group_desc" id="group_desc"></textarea>
                </div>
                <div class="formbutton">
                    <a class="cancelbtn" onclick="document.getElementById('create_group_form').style.display='none'">Cancel</a>
                    <button class="createbtn" type="submit">Create Group</button>
                </div>
            </form>
        </div>
    </div>
</div>