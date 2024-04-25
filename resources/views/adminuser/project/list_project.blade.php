@extends('layouts.app_client')

@section('navigationbar')
@endsection

@section('notification')
    @if(session('notification'))
        <div class="notificationlayer">
            <div class="notification" id="notification">
                <image class="notificationicon" src="{{ url('template/images/icon_menu/checklist.png') }}"></image>
                <p class="notificationtext">{{ session('notification') }}</p>
            </div>
        </div>
    @endif
@endsection

@section('content')
	<style type="text/css">
		.tableProjects td {
			vertical-align: middle;
		}

		.tableProjects{
		    border-collapse: separate;
		    border:1px solid #F1F1F1;
		    border-radius: 7px;
		    width:100%
		}

		.tableProjects th {
		    padding: 15px 0px 15px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:14px;
		    font-weight:600;
		}

		.tableProjects td  {
		    padding: 13px 0px 13px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:13.5px;
		    color:black;
		}

		.tableProjects tbody tr:last-child td{
		    border-bottom: none;
		}

		.tableProjects tbody tr:hover {
		    background-color: #f0f0f0;
		}

		.modal-content {
		    -webkit-border-radius: 0px !important;
		    -moz-border-radius: 0px !important;
		    border-radius: 10px !important; 
		}

		.notificationlayer {
	        position: absolute;
	        width:100%;
	        height:50px;
	        z-index: 1;
	        pointer-events: none;
	    }

	    #notification {
	        background-color: #FFFFFF;
	        border: 2px solid #12B76A;
	        border-radius: 8px;
	        display: flex;
	        color: #232933;
	        margin: 50px auto;
	        text-align: center;
	        height: 48px;
	        position: absolute;
	        top: 0;
	        left: 50%;
	        transform: translateX(-50%);
	        transition: top 0.5s ease;    
	    }

	    .notificationicon {
	        width:20px;
	        height:20px;
	        margin-top:11px;
	        margin-left:15px;
	    }

	    .notificationtext{
	        margin-top:11px;
	        margin-left:8px;
	        margin-right:13px;
	        font-size:14px;
	    }

	    .btn-custom-act {
	    	background: transparent;
	    	border: solid 1px #EDF0F2;
	    	border-radius: 8px;
	    	color: #1570EF;
	    	font-weight: 600;
	    }

	    .image-project {
	    	border: solid 1px #EDF0F2;
	    	width: 48px;
	    	height: 48px;
	    	border-radius: 8px;
	    	position: relative;
	    }

	    .image-project img {
	    	width: 32px;
	    	height: 32px;
	    	margin: 0;
		  	position: absolute;
		  	top: 50%;
		  	left: 50%;
		  	-ms-transform: translate(-50%, -50%);
		  	transform: translate(-50%, -50%);
	    }

	    .title-project h3 {
	    	font-size: 16px;
	    	font-weight: 600;
	    	letter-spacing: 0.5%;
	    	color: #1D2939;
	    	line-height: 0;
	    }

	    .title-project span {
	    	font-weight: 400;
	    	font-size: 12px;
	    	line-height: 20px;
	    	letter-spacing: 0.5%;
	    	color: #586474;
	    }

		.title-subproject {
			margin-left: 65px;
		}

		.title-subproject h3 {
	    	font-size: 16px;
	    	font-weight: 600;
	    	letter-spacing: 0.5%;
	    	color: #1D2939;
	    	line-height: 0;
	    }

		.title-subproject a {
			color: #1D2939;
		}

	    .title-subproject span {
	    	font-weight: 400;
	    	font-size: 12px;
	    	line-height: 20px;
	    	letter-spacing: 0.5%;
	    	color: #586474;
	    }

	    .label-users{
		    background: #EDF0F2;
		    font-size:12px;
		    font-weight:600;
		    color: #1D2939;
		    padding:5px 10px 5px 10px;
		    border-radius:25px;
		}

		.table thead > tr > th { border-top: none; }
		.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td { border-bottom: 1px solid #ddd; }
		
		.modal-content {
	        padding: 0px !important;
	    }

	    .modal-body {
	        padding: 25px !important;
	    }

	    .custom-modal-header {
	        padding: 5px;
	        width: 95%;
	        margin: 0 auto;
	        margin-top: 13px;
	    }

	    .custom-form input {
	        border-radius: 7px;
	    }

	    .custom-form select {
	        border-radius: 7px;
	    }

	    .custom-form textarea {
	        border-radius: 7px;
	    }

		.child-row-general {
			display: none;
		}
	</style>
	<div class="pull-left">
		<h3 style="color:black;font-size:28px;">Project</h3>
	</div>
	<div class="pull-right" style="margin-bottom: 24px; margin-top:10px;">
		<a href="#modal-add-subproject" data-toggle="modal" class="btn btn-md btn-default" style="border-radius: 9px; color:#1570EF; font-weight:bold;">Create Subroject</a>
		<a href="#modal-add-project" data-toggle="modal" class="btn btn-md btn-primary" style="border-radius: 9px;"><image src="{{ url('template/images/icon_menu/add.png') }}" width="24" height="24"> Create Project</a>
	</div><div style="clear: both;"></div>

	<div class="alert alert-icon alert-info alert-dismissible fade in" role="alert" style="background-color:#EFF8FF; border:solid 1px #EFF8FF; color:#1D2939;">
		<button type="button" class="close" data-dismiss="alert"
				aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<i class="mdi mdi-information"></i>
		Add a subproject so start uploading files.
	</div>
	@if(count($project) > 0)
	<table class="table table-hover custom-table">
		<tbody>
			@foreach($project as $key => $projects)
				<tr class="">
					<td width="50" style="vertical-align: middle;" align="center">
						@if(count($projects->RefSubProject) > 0)
							<a href="javascript:void(0)" data-key="{{ $key }}" onclick="slideData(this)"><span class="caret"></span></a>
						@endif
					</td>
					<td width="48">
						<div class="image-project">
							<img src="{{ url('template/images/icon-projects1.png') }}">
						</div>
					</td>
					<td style="vertical-align: middle;">
						<div class="title-project">
							<h3><a href="">{{ $projects->project_name }}</a></h3>
							<span style="color:#1D2939;">{{ App\Helpers\GlobalHelper::formatBytes(DB::table('upload_files')->where('project_id', $projects->project_id)->sum('size')) }}</span> <span style="color:#586474;">{{ !empty($projects->project_desc) ? "- ".$projects->project_desc : '' }}</span>
						</div>
					</td>
					<td style="vertical-align: middle;" width="100">
						<div class="dropdown">
							<button class="btn btn-md dropdown-toggle btn-custom-act" type="button" data-toggle="dropdown">
								Action&nbsp; <span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right">
								<li><a href="#modal-add-project" data-toggle="modal" data-title="Edit Project" data-query="{{ $projects }}" onclick="getDetailProject(this)"><i class="fa fa-edit"></i> Edit</a></li>
								<span class="divider"></span>
								<li><a href="#modal-terminate-project" data-toggle="modal" class="text-danger" data-projectid="{{ $projects->project_id }}" onclick="getProjectId(this)"><i class="fa fa-times"></i> Terminate Project</a></li>
							</ul>
						</div>
					</td>
				</tr>
				@foreach($projects->RefSubProject as $subs)
					<tr class="child-row-general child-row{{ $key }}">
						<td></td>
						<td colspan="2">
							<div class="title-subproject">
								<h3 style="color:#1D2939;"><a href="{{ route('adminuser.documents.list', base64_encode($subs->project_id.'/'.$subs->subproject_id)) }}">{{ $subs->subproject_name }}</a></h3>
								<span style="color:#1D2939;">{{ App\Helpers\GlobalHelper::formatBytes(DB::table('upload_files')->where('subproject_id', $subs->subproject_id)->sum('size')) }}</span>
							</div>
						</td>
						<td style="vertical-align: middle;" width="100">
							<div class="dropdown">
								<button class="btn btn-md dropdown-toggle btn-custom-act" type="button" data-toggle="dropdown">
									Action&nbsp; <span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="#modal-add-subproject" data-toggle="modal" data-title="Edit Sub Project" data-query="{{ $subs }}" onclick="getDetailSubProject(this)"><i class="fa fa-edit"></i> Edit</a></li>
									<li><a href="#modal-permissions" data-toggle="modal"><i class="fa fa-lock"></i> Permissions</a></li>
									<li><a href="{{ route('project.delete-sub-project', $subs->subproject_id) }}" onclick="return confirm('are you sure delete this item ?')" class="text-danger"><i class="fa fa-trash"></i> Delete</a></li>
								</ul>
							</div>
						</td>
					</tr>
				@endforeach
			@endforeach
		</tbody>
	</table>
	@else
		<div class="card-box">
			<center>
				<img src="{{ url('template/images/empty_project.png') }}" width="300" />
			</center>    
		</div>
	@endif

	<div id="modal-add-project" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                	<div class="custom-modal-header">
                		<button type="button" onclick="reloadPage()" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                		<div style="float: left;">
	                        <img src="{{ url('template/images/data-project.png') }}" width="24" height="24">
	                    </div>
	                    <div style="float: left; margin-left: 10px;">
	                        <h4 class="modal-title" id="titleModal">
	                        	Create Project
	                        </h4>
	                    </div>
	                </div>
                </div>
                <div class="modal-body">
                	<form class="custom-form" action="{{ route('project.save-project') }}" method="POST">
						@csrf
						<input type="hidden" name="id" id="id">
						<div class="form-group">
							<label>Project Name <span class="text-danger">*</span></label>
							<input required type="text" name="project_name" id="project_name" class="form-control">
						</div>
						
						<div class="form-group">
							<label>Project Desc </label>
							<textarea name="project_desc" id="project_desc" class="form-control"></textarea>
						</div>
						<div class="pull-right">
							<button type="button" data-dismiss="modal" class="btn btn-default" style="border-radius: 5px;">
								Cancel
							</button>
							<button type="submit" class="btn btn-primary" style="border-radius: 5px;">
								Create
							</button>
						</div><div style="clear:both;"></div>
                	</form>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-add-subproject" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="custom-modal-header">
                		<button type="button" onclick="reloadPage()" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                		<div style="float: left;">
	                        <img src="{{ url('template/images/data-project.png') }}" width="24" height="24">
	                    </div>
	                    <div style="float: left; margin-left: 10px;">
	                        <h4 class="modal-title" id="titleModalSub">
	                        	Create Subproject
	                        </h4>
	                    </div>
	                </div>
                </div>
                <div class="modal-body">
					<form class="custom-form" action="{{ route('project.save-subproject') }}" method="POST">
						@csrf
						<input type="hidden" name="id" id="idSubProject">
						<div class="form-group" id="parentProjectID">
							<label>Project Parents <span class="text-danger">*</span></label>
							<select required name="project_id" class="form-control select2">
								@if(count($parentProject) > 0)
									@foreach($parentProject as $parents)
										<option value="{{ $parents->project_id }}">{{ $parents->project_name }}</option>
									@endforeach
								@endif
							</select>
						</div>
						<div class="form-group">
							<label>Subproject Name <span class="text-danger">*</span></label>
							<input required type="text" name="project_name" id="subproject_name" class="form-control">
						</div>
						<div class="pull-right">
							<button type="button" data-dismiss="modal" class="btn btn-default" style="border-radius: 5px;">
								Cancel
							</button>
							<button type="submit" class="btn btn-primary" style="border-radius: 5px;">
								Create
							</button>
						</div><div style="clear:both;"></div>
                	</form>
                </div>
            </div>
        </div>
    </div>

	<div id="modal-terminate-project" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                	<div class="custom-modal-header">
                		<button type="button" onclick="reloadPage()" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                		<div style="float: left;">
	                        <img src="{{ url('template/images/data-project.png') }}" width="24" height="24">
	                    </div>
	                    <div style="float: left; margin-left: 10px;">
	                        <h4 class="modal-title" id="titleModal">
	                        	Terminate Project
	                        </h4>
	                    </div>
	                </div>
                </div>
                <div class="modal-body">
					<div class="alert alert-warning" role="alert">
						<i class="fa fa-warning"></i> Access to the data room will be immediately terminated for all users, including project administrators.
					</div>
                	<form class="custom-form" action="{{ route('project.terminate-project') }}" method="POST">
						@csrf
						<input type="hidden" name="project_id" id="project_id">
						<div class="form-group">
							<label>Terminate reason <span class="text-danger">*</span></label>
							<select required class="form-control" name="terminate_reason" id="terminate_reason">
								<option value="">- select reason -</option>
								<option value="Project canceled">Project canceled</option>
								<option value="Project on hold">Project on hold</option>
								<option value="Project finalized">Project finalized</option>
								<option value="Project on hold">Project on hold</option>
								<option value="I'm looking for alternative VDR">I'm looking for alternative VDR</option>
								<option value="I don't want to disclose">I don't want to disclose</option>
							</select>
						</div>
						<div class="pull-right">
							<button type="button" data-dismiss="modal" class="btn btn-default" style="border-radius: 5px;">
								Cancel
							</button>
							<button type="submit" class="btn btn-danger" style="border-radius: 5px;">
								Confirm & terminate
							</button>
						</div><div style="clear:both;"></div>
                	</form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
	<script type="text/javascript">
		function slideData(element){
			var idx = $(element).data('key');
			$(".child-row"+idx).toggle();
		}

		$(document).ready(function () {
           	$('#tableProjects').dataTable();
        });

        function getDetailProject(element) {
        	var title = $(element).data('title');
        	var query = $(element).data('query');
        	$("#titleModal").html(title);

        	$("#id").val(query.project_id);
        	$("#project_name").val(query.project_name);
        	$("#project_desc").val(query.project_desc);
    	}

		function getDetailSubProject(element) {
        	var title = $(element).data('title');
        	var query = $(element).data('query');
        	$("#titleModalSub").html(title);
			
			$("#parentProjectID").css("display", "none");

        	$("#idSubProject").val(query.id);
        	$("#subproject_name").val(query.subproject_name);
        	$("#subproject_desc").val(query.subproject_desc);
    	}

		function getProjectId(element) {
			var project_id = $(element).data('projectid');
			$("#project_id").val(project_id);
		}

    	function reloadPage() {
    		location.reload();
    	}

    	function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();
	</script>
@endpush