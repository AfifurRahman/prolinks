<div id="modal-add-discussion" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="custom-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="float: left;">
                        <img src="{{ url('template/images/data-company.png') }}" width="24" height="24">
                    </div>
                    <div style="float: left; margin-left: 10px;">
                        <h4 class="modal-title" id="titleModal">
                        	Create Discussion
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
            	<form class="custom-form" action="{{ route('discussion.save-discussion') }}" method="POST" enctype="multipart/form-data">
            		@csrf
            		<input type="hidden" name="id" id="id">
            		<div class="form-group">
            			<label>Subject <span class="text-danger">*</span></label>
            			<input required type="text" name="subject" id="subject" class="form-control" placeholder="Enter subject">
            		</div>
            		<div class="form-group">
            			<label>Discussion</label>
                		<textarea class="form-control" id="description" name="description"></textarea>
					</div>
                    <div class="form-group">
                        <label>Link Document <span class="text-muted">( optional )</span></label>
                        <select class="form-control" name="link_document" id="link_document">
                            <option value="">- select link document -</option>
                        </select>
                    </div>
            		<div class="form-group">
                        <label>Attach File</label>
                        <input type="file" name="attach_file" id="attach_file" class="form-control">
                    </div>
            		<div class="pull-right">
            			<button type="button" data-dismiss="modal" class="btn btn-default" style="border-radius: 5px;">
            				Close
            			</button>
            			<button type="submit" class="btn btn-primary" style="border-radius: 5px;">
            				Create
            			</button>
            		</div> <div style="clear: both;"></div>
            	</form>
            </div>
        </div>
    </div>
</div>