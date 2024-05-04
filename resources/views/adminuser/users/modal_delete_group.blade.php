<div id="modal-delete-group" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="custom-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="titleModal">
                        Delete Group ?
                    </h4>
                </div>
            </div>
            <div class="modal-body">
                <label>Are you sure delete this group ?</label><br><br>
                <input type="hidden" id="get_url_delete_group">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" onclick="actDeleteGroup()" class="btn btn-danger">Delete</button>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>