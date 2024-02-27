@extends('layouts.app_client')

@section('navigationbar')
	<a href="#modal-add-project" data-toggle="modal" class="btn btn-lg btn-rounded btn-primary"><i class="fa fa-plus-circle"></i> New Project</a>
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
	<script type="text/javascript">
		var title = document.getElementById('title');
		title.textContent = "List Project";
	</script>
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
	</style>

	<table id="tableProjects" class="tableProjects">
		<thead>
			<tr>
				<th>No</th>
				<th>Project Name</th>
				<th>Assign Role</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@if(count($project) > 0)
				@foreach($project as $key => $projects)
					<tr>
						<td width="80">{{ $loop->iteration }}</td>
						<td>{{ $projects->project_name }}</td>
						<td>
							@if(!empty($projects->RefClientGroup->group_name))
								<label class="label label-inverse" style="border-radius: 10px;">{{ $projects->RefClientGroup->group_name }}</label>
								<div style="margin-top: 3px;">
									<a href="#modal-view-role" data-toggle="modal" data-id="{{ $projects->role_group_id }}" onclick="getRole(this)">view role <i class="fa fa-search"></i></a>
								</div>
							@else
								<span class="text-muted"><i>not set</i></span>
							@endif
						</td>
						<td width="150">
							<div class="dropdown">
								<button class="btn btn-md dropdown-toggle" type="button" data-toggle="dropdown" style="border: solid 0px; background: transparent;">
									<i class="fa fa-ellipsis-v"></i>
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
                    <button type="button" onclick="reloadPage()" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="titleModal">Add Project</h4>
                </div>
                <div class="modal-body">
                	<form action="{{ route('project.save-project') }}" method="POST">
	                	<div class="row">
	                		<div class="col-md-6">
								@csrf
								<input type="hidden" name="id" id="id">
		                		<div class="form-group">
									<label>Role Group<span class="text-danger">*</span></label>
									<select name="role_group_id" id="role_group_id" onchange="changeListRole(this)" class="form-control select2">
										<option value="">- select role group -</option>
										@foreach($client_group as $groups)
											<option value="{{ $groups->id }}">{{ $groups->group_name }}</option>
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
	                		<div class="col-md-12">
	                			<div class="form-group">
									<button type="submit" class="btn btn-primary col-md-12">
										<i class="fa fa-check"></i> Submit
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
        	getDetailRole(id);
        }

        function changeListRole(element) {
        	var id = element.value;
        	getDetailRole(id);
        }

        function getDetailRole(id) {
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
		        						res += "<td><label class='label label-success label-rounded'>Administrator</label></td>"
		        					}else if(output[i].role == 1){
		        						res += "<td><label class='label label-info label-rounded'>Collaborator</label></td>"
		        					}
		        				res += "</tr>"
	        				}
	        				
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
        	$("#role_group_id").val(query.role_group_id).trigger('change');
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