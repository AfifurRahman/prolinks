<div id="modal-create-group" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="custom-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="float: left;">
                        <img id="inviteuser-ico" src="{{ url('template/images/icon_menu/group.png') }}" width="24" height="24">
                    </div>
                    <div style="float: left; margin-left: 10px;">
                        <h4 class="modal-title" id="titleModal">
                            Create Group
                        </h4>
                    </div>
                </div>
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
                        <a class="cancelbtn" data-dismiss="modal">Cancel</a>
                        <button class="createbtn" type="submit">Create Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>