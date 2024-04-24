<div id="modal-confirm-status-close" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="custom-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="titleModal">
                        Close Question ?
                    </h4>
                </div>
            </div>
            <div class="modal-body">
                <form action="{{ route('discussion.change-status-qna-closed') }}" method="POST">
                    @csrf
                    <input type="hidden" id="discussion_id" name="discussion_id" value="{{ !empty($detail->discussion_id) ? $detail->discussion_id : null }}">
                    <label>Once closed, no follow-up questions can be asked. Do you want to proceed?</label>
                    <div class="pull-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Yes, close</button>
                    </div>
                </form>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>