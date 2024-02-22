@extends('layouts.app_client')

@section('content')
	<script type="text/javascript">
		var title = document.getElementById('title');
		title.textContent = "Create Project";
	</script>
	<style type="text/css">
		.custom-box-first-project {
			width: 60%;
			/*margin: 0 auto;*/
		}

		body.modal-open .custom-box-first-project{
		    -webkit-filter: blur(1px);
		    -moz-filter: blur(1px);
		    -o-filter: blur(1px);
		    -ms-filter: blur(1px);
		    filter: blur(1px);
		}

		body.modal-open .topbar{
		    -webkit-filter: blur(1px);
		    -moz-filter: blur(1px);
		    -o-filter: blur(1px);
		    -ms-filter: blur(1px);
		    filter: blur(1px);
		}

		body.modal-open .side-menu{
		    -webkit-filter: blur(1px);
		    -moz-filter: blur(1px);
		    -o-filter: blur(1px);
		    -ms-filter: blur(1px);
		    filter: blur(1px);
		}

		.modal-content {
		    -webkit-border-radius: 0px !important;
		    -moz-border-radius: 0px !important;
		    border-radius: 10px !important; 
		}
	</style>

	<!-- Modal -->
	<div id="modal-create-new-project" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            	<div class="modal-header">
                	<h4>Create First Project</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-icon alert-success">
						<i class="mdi mdi-information"></i> For first step, please create your project
					</div>
					<form action="{{ route('project.save-first-project') }}" method="POST">
						@csrf
						<div class="form-group">
							<label>Project Name <span class="text-danger">*</span></label>
							<input required type="text" name="project_name" id="project_name" class="form-control">
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Start Date</label>
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</span>
										<input type="date" name="start_date" id="start_date" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Deadline</label>
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</span>
										<input type="date" name="deadline" id="deadline" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Project Desc </label>
							<textarea name="project_desc" id="project_desc" class="form-control"></textarea>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary col-md-12">
								<i class="fa fa-check"></i> Create Project
							</button>
						</div><div style="clear: both;"></div>			
					</form>
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
	<script type="text/javascript">
		$(document).ready(function(){
			$("#modal-create-new-project").modal();
		});
	</script>
@endpush