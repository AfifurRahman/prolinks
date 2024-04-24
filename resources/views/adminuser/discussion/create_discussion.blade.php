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
                <form class="custom-form" id="fileForm" enctype="multipart/form-data">
            	<!-- <form class="custom-form" action="{{ route('discussion.save-discussion') }}" method="POST" enctype="multipart/form-data"> -->
            		@csrf
            		<input type="hidden" name="id" id="id">
            		<div class="form-group">
            			<label>Subject <span class="text-danger">*</span></label>
            			<input required type="text" name="subject" id="subject" class="form-control" placeholder="Enter subject">
            		</div>
                    <div class="form-group">
            			<label>Priority <span class="text-danger">*</span></label>
                        <select name="priority" id="priority" class="form-control">
                            <option value="">- select priority -</option>
                            <option value="1">Low</option>
                            <option value="2">Medium</option>
                            <option value="3">High</option>
                        </select>
                	</div>
            		<div class="form-group">
            			<label>Discussion <span class="text-danger">*</span></label>
                		<textarea required class="form-control" id="description" name="description"></textarea>
					</div>
                    <div class="form-group" style="margin-top:5px;">
                        <div style="float:left">
                            <a href="#modal-link-file" data-toggle="modal" class="btn btn-default radius-button" style="color:#1570EF;"><i class="fa fa-paperclip"></i> Select from dataroom</a>
                        </div>
                        <div style="float:left; margin-left:10px;">
                            <input type="file" class="btn btn-default radius-button" style="color:#1570EF;" name="upload_doc[]" id="upload_doc" multiple />
                        </div> <div style="clear:both;"></div>
                        <div id="result-link-file"></div>
                        <div id="result-upload-file"></div>
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

<div id="modal-link-file" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
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
                            Select from dataroom
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    @foreach($file as $files)
                        <tr>
                            <td><input type="checkbox" value="{{ $files->id }}" data-filename="{{ $files->name }}" name="link_document" id="link_document" /> {{ $files->name }} </td>
                        </tr>
                    @endforeach
                </table>
                
                <button type="button" class="btn btn-primary" onclick="getLinkDoc()">Apply</button>
            </div>
        </div>
    </div>
</div>