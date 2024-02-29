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
	    	font-size: 14px;
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
	</style>
	<div class="pull-left">
		<h3 style="color:black;font-size:28px;">Project</h3>
	</div>
	<div class="pull-right" style="margin-bottom: 24px;">
		<a href="#modal-add-project" data-toggle="modal" class="btn btn-md btn-primary" style="border-radius: 9px;"><image src="{{ url('template/images/icon_menu/add.png') }}" width="24" height="24"> Create Project</a>
	</div><div style="clear: both;"></div>
	<table class="table table-hover custom-table">
		<tbody>
			@if(count($project) > 0)
				@foreach($project as $key => $projects)
					<tr>
						<td width="48">
							<div class="image-project">
								<img src="{{ url('template/images/icon-projects1.png') }}">
							</div>
						</td>
						<td style="vertical-align: middle;">
							<div class="title-project">
								<h3><a href="">{{ $projects->project_name }}</a></h3>
								<span>Last session : {{ date('d M Y H:i') }}</span>
							</div>
						</td>
						<td style="vertical-align: middle;" width="100">
							<div class="dropdown">
								<button class="btn btn-md dropdown-toggle btn-custom-act" type="button" data-toggle="dropdown">
									Action&nbsp; <span class="caret"></span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="#modal-add-project" data-toggle="modal" data-title="Edit Project" data-query="{{ $projects }}" onclick="getDetailProject(this)"><i class="fa fa-edit"></i> Edit</a></li>
								   	<li><a href="{{ route('project.delete-project', $projects->project_id) }}" onclick="return confirm('are you sure delete this item ?')"><i class="fa fa-trash"></i> Delete</a></li>
							  	</ul>
							</div>
						</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	</table>

	<div id="modal-add-project" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
	                	<div class="row">
	                		<div class="col-md-6">
								@csrf
								<input type="hidden" name="id" id="id">
		                		<div class="form-group">
									<label>Company<span class="text-danger">*</span></label>
									<select required name="company_id" id="company_id" onchange="changeCompany(this)" class="form-control select2">
										<option value="">- select company -</option>
										@foreach($company as $companies)
											<option value="{{ $companies->company_id }}">{{ $companies->company_name }}</option>
										@endforeach
									</select>
								</div>
		                		<div class="form-group">
									<label>Project Name <span class="text-danger">*</span></label>
									<input required type="text" name="project_name" id="project_name" class="form-control">
								</div>
								
								<div class="form-group">
									<label>Project Desc </label>
									<textarea name="project_desc" id="project_desc" class="form-control"></textarea>
								</div>
	                		</div>
	                		<div class="col-md-6">
	                			<label>List Access Users</label>
	                			<table class="tableProjects">
	                				<thead>
	                					<tr>
	                						<th>Username / Email</th>
	                						<th>Type</th>
	                					</tr>
	                				</thead>
	                				<tbody class="resultUserGroup"></tbody>
	                			</table>
	                		</div>
	                	</div>
	                	<div class="row">
	                		<div class="col-md-6">
	                			<div class="pull-right">
	                				<button type="button" data-dismiss="modal" class="btn btn-default" style="border-radius: 5px;">
			            				Close
			            			</button>
									<button type="submit" class="btn btn-primary" style="border-radius: 5px;">
										Create
									</button>
								</div>
	                		</div>
	                	</div>
                	</form>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-view-role" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">List Access Users</h4>
                </div>
                <div class="modal-body">
                	<table class="tableProjects">
        				<thead>
        					<tr>
        						<th>Username / Email</th>
        						<th>Type</th>
        					</tr>
        				</thead>
        				<tbody class="resultUserGroup"></tbody>
        			</table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
	<script type="text/javascript">
		$(document).ready(function () {
           	$('#tableProjects').dataTable();
        });

        function getRole(element) {
        	var id = $(element).data('id');
        	getDetailCompany(id);
        }

        function changeCompany(element) {
        	var id = element.value;
        	getDetailCompany(id);
        }

        function getDetailCompany(id) {
        	if (id != "") {
	        	$.ajax({
	        		url: "{{ route('project.detail-role-users') }}",
	        		type: "POST",
	        		data: {
	        			"_token": "{{ csrf_token() }}",
	        			"id": id
	        		},
	        		beforeSend:function(){
	        			var res = "<tr><td colspan='2' align='center'>loading..</td></tr>";
	        			$(".resultUserGroup").html(res);
	        		},

	        		success:function(output){
	        			if (output.length > 0) {
	        				var res = ""
	        				for (var i = 0; i < output.length; i++) {
	        					res += "<tr>"
		        					res += "<td>"+output[i].email_address+"</td>"
		        					if (output[i].role == 0) {
		        						res += "<td><label class='label-users'>Administrator</label></td>"
		        					}else if(output[i].role == 1){
		        						res += "<td><label class='label-users'>Collaborator</label></td>"
		        					}
		        				res += "</tr>"
	        				}
	        				
	        				$(".resultUserGroup").html(res);
	        			}else{
	        				var res = "<tr><td colspan='2' align='center'>not found</td></tr>";
	        				$(".resultUserGroup").html(res);
	        			}
	        		}
	        	});
	        }else{
	        	$(".resultUserGroup").html("");
	        }
        }

        function getDetailProject(element) {
        	var title = $(element).data('title');
        	var query = $(element).data('query');
        	
        	$("#titleModal").html(title);

        	$("#id").val(query.project_id);
        	$("#project_name").val(query.project_name);
        	$("#company_id").val(query.company_id).trigger('change');
    		$("#project_desc").val(query.project_desc);
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