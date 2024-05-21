@extends('layouts.app_backend')

@section('content')
	<script type="text/javascript">
		var title = document.getElementById('title');
		title.textContent = "Monitoring Client";
	</script>
	<style type="text/css">
		#tableMonitoringClients td {
			vertical-align: middle;
		}

		#tableMonitoringClients{
		    border-collapse: separate;
		    border:1px solid #F1F1F1;
		    border-radius: 7px;
		    width:100%
		}

		#tableMonitoringClients th {
		    padding: 15px 0px 15px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:14px;
		    font-weight:600;
		}

		#tableMonitoringClients td  {
		    padding: 13px 0px 13px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:13.5px;
		    color:black;
		}

		#tableMonitoringClients tbody tr:last-child td{
		    border-bottom: none;
		}

		#tableMonitoringClients tbody tr:hover {
		    background-color: #f0f0f0;
		}
	</style>
	<table id="tableMonitoringClients">
		<thead>
			<tr>
				<th>No</th>
				<th>Client</th>
				<th>User</th>
				<th>Role</th>
				<th>Status</th>
				<th>Created At</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@if(count($clients) > 0)
				@foreach($clients as $key => $client)
					<tr>
						<td>{{ $loop->iteration }}</td>
						<td>{{ !empty($client->RefClient->client_name) ? $client->RefClient->client_name : '' }}</td>
						<td>{!! !empty($client->name) ? $client->name.'<br>' : '' !!} <span class="text-muted">{{ $client->email_address }}</span></td>
						<td>
							@if($client->role == 0) 
								<span class="label label-default" style="background-color: #D7D7D7; border-radius: 10px; color:#000;">Administrator</span>
							@elseif($client->role == 1)
								<span class="label label-default" style="background-color: #D7D7D7; border-radius: 10px; color:#000;">Collaborator</span>
							@elseif($client->role == 2)
								<span class="label label-default" style="background-color: #D7D7D7; border-radius: 10px; color:#000;">Reviewer</span>
							@endif
						</td>
						<td>
							@if($client->status == 1)
								<span class="label label-success">Active</span>
							@elseif($client->status == 2)
								<span class="label label-danger">Disabled</span>
							@elseif($client->status == 0)
								<span class="label label-inverse">Invited</span>
							@endif
						</td>
						<td>
							{{ $client->created_at }}
						</td>
						<td>
							@if(\role::get_permission(array('detail-monitoring')))
								@php
									$tabs = "";
									if($client->role == 0){
										$tabs = "dashboard";
									}else{
										$tabs = "documents";	
									}
								@endphp
								<a href="{{ route('backend.monitoring.detail', base64_encode($client->id)."?tab=".$tabs) }}" target="_blank" data-toggle="tooltip" title="access dashboard user"><i class="fa fa-search"></i></a>
							@endif
						</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	</table>
@stop

@push('scripts')
	<script type="text/javascript">
		$(document).ready(function () {
           	$('#tableMonitoringClients').dataTable();
        });
	</script>
@endpush