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
				<th>Client Name</th>
				<th>Package Type</th>
				<th>Allocation Size</th>
				<th>Allocation Usage</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@if(count($clients) > 0)
				@foreach($clients as $key => $client)
					<tr>
						<td>{{ $loop->iteration }}</td>
						<td>{{ $client->client_name }}</td>
						<td>{{ !empty($client->RefPricing->pricing_name) ? $client->RefPricing->pricing_name : '' }}</td>
						<td>0</td>
						<td>0</td>
						<td>
							@if(\role::get_permission(array('detail-monitoring')))
								<a href="{{ route('backend.monitoring.detail', $client->client_id) }}" data-toggle="tooltip" title="detail monitoring"><i class="fa fa-search"></i></a>
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