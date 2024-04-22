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
            			<label>Project <span class="text-danger">*</span></label>
            			<select name="project_id" id="project_id" class="form-control">
                            <option value="">- select project -</option>
                            @foreach($project as $projects)
                                <option value="{{ $projects->project_id }}">{{ $projects->project_name }}</option>
                            @endforeach
                        </select>
            		</div>
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
                    <div class="form-group">
                        <label>Select from dataroom</label>
                        <select class="form-control select2" multiple name="link_doc[]" id="link_doc">
                            <option value="">- Select from dataroom -</option>
                            @foreach($file as $files)
                                <option value="{{ $files->id }}">{{ $files->name }}</option>
                            @endforeach
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