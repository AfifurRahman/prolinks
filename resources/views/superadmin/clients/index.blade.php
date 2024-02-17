@extends('layouts.app_backend')

@section('content')
	<style type="text/css">
		#tableClients td {
			vertical-align: middle;
		}
	</style>
	<div class="card-box">
		<a href="{{ route('backend.client.add') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Client</a>
		<a href="{{ route('backend.client.list') }}" data-toggle="tooltip" title="reload page" class="btn btn-success"><i class="fa fa-refresh"></i></a><br><br>
		<table id="tableClients" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>No</th>
					<th>Client Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>Address</th>
					<th>City</th>
					<th>Pricing</th>
					<th>Status</th>
					<th>#</th>
				</tr>
			</thead>
			<tbody>
				@if(count($clients) > 0)
					@foreach($clients as $key => $client)
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $client->client_name }}</td>
							<td>{{ $client->client_email }}</td>
							<td>{{ $client->client_phone }}</td>
							<td>{{ $client->client_address }}</td>
							<td>{{ $client->client_city }}</td>
							<td>{{ !empty($client->RefPricing->pricing_name) ? $client->RefPricing->pricing_name : '' }}</td>
							<td>{!! \globals::label_status($client->client_status) !!}</td>
							<td>
								<div class="dropdown">
									<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Action
									<span class="caret"></span></button>
									<ul class="dropdown-menu">
									    <li><a href="{{ route('backend.client.send-email', $client->client_id) }}" onclick="return confirm('are you sure send email this item ?')"><i class="fa fa-envelope"></i> Send Email</a></li>
									    <li><a href="{{ route('backend.client.edit', $client->client_id) }}"><i class="fa fa-edit"></i> Edit</a></li>
									    <li><a href="{{ route('backend.client.delete', $client->client_id) }}" onclick="return confirm('are you sure delete this item ?')"><i class="fa fa-trash"></i> Delete</a></li>
								  	</ul>
								</div>
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
           	$('#tableClients').dataTable();
        });
	</script>
@endpush