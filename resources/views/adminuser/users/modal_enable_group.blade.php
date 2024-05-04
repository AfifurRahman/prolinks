<div id="modal-enable-group" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="custom-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="titleModal">
                        Enable Group ?
                    </h4>
                </div>
            </div>
            <div class="modal-body">
                <label>Enable a group will also enable all group members from assigned projects.</label><br><br>
                <input type="hidden" id="get_url_enable_group">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" onclick="actEnableGroup()" class="btn btn-primary">Enable</button>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>