@extends('layouts.app_backend')

@section('content')
	<style type="text/css">
		#tableMonitoringClients td {
			vertical-align: middle;
		}
	</style>
	<div class="card-box">
		<table id="tableMonitoringClients" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>No</th>
					<th>Client Name</th>
					<th>Package Type</th>
					<th>Allocation Size</th>
					<th>Allocation Usage</th>
					<th>#</th>
				</tr>
			</thead>
			<tbody>
				@if(count($clients) > 0)
					@foreach($clients as $key => $client)
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $client->client_name }}</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>
								@if(\role::get_permission(array('detail-monitoring')))
									<a href="{{ route('backend.monitoring.detail', $client->client_id) }}" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></a>
								@endif
							</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
	</div>
@stop

@push('scripts')
	<script type="text/javascript">
		$(document).ready(function () {
           	$('#tableMonitoringClients').dataTable();
        });
	</script>
@endpush