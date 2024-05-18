@extends('layouts.app_backend')

@section('content')
	<script type="text/javascript">
		var title = document.getElementById('title');
		title.textContent = "Manage Client";
	</script>
	<style type="text/css">
		#tableClients td {
			vertical-align: middle;
		}

		#tableClients{
		    border-collapse: separate;
		    border:1px solid #F1F1F1;
		    border-radius: 7px;
		    width:100%
		}

		#tableClients th {
		    padding: 15px 0px 15px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:14px;
		    font-weight:600;
		}

		#tableClients td  {
		    padding: 13px 0px 13px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:13.5px;
		    color:black;
		}

		#tableClients tbody tr:last-child td{
		    border-bottom: none;
		}

		#tableClients tbody tr:hover {
		    background-color: #f0f0f0;
		}
	</style>
	<div class="pull-right">
		@if(\role::get_permission(array('add-client')))
			<a href="{{ route('backend.client.add') }}" class="btn btn-lg btn-rounded btn-primary"><i class="fa fa-plus-circle"></i> Add Client</a>
		@endif
		<a href="{{ route('backend.client.list') }}" data-toggle="tooltip" data-placement="bottom" title="reload page" class="btn btn-lg btn-rounded btn-success"><i class="fa fa-refresh"></i></a>
	</div><div style="clear:both;"></div> <br>
	<table id="tableClients">
		<thead>
			<tr>
				<th>No</th>
				<th>Client Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Address</th>
				<th>Pricing</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@if(count($clients) > 0)
				@foreach($clients as $key => $client)
					<tr>
						<td width="50">{{ $loop->iteration }}</td>
						<td>{{ $client->client_name }}</td>
						<td>{{ $client->client_email }}</td>
						<td>{{ $client->client_phone }}</td>
						<td>{{ $client->client_address }}</td>
						<td>{{ !empty($client->RefPricing->pricing_name) ? $client->RefPricing->pricing_name : '' }}</td>
						<td>{!! \globals::label_status($client->client_status) !!}</td>
						<td width="80">
							<div class="dropdown">
								<button class="btn btn-md dropdown-toggle" type="button" data-toggle="dropdown" style="border: solid 0px; background: transparent;">
									<i class="fa fa-ellipsis-v"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									@if(\role::get_permission(array('send-email-client')))
								    	<li><a href="{{ route('backend.client.send-email', $client->client_id) }}" onclick="return confirm('are you sure send email this item ?')"><i class="fa fa-envelope"></i> Send Email</a></li>
								    @endif

								    @if(\role::get_permission(array('edit-client')))
								    	<li><a href="{{ route('backend.client.edit', $client->client_id) }}"><i class="fa fa-edit"></i> Edit</a></li>
								    @endif

								    @if(\role::get_permission(array('delete-client')))
								    	<li><a href="{{ route('backend.client.delete', $client->client_id) }}" onclick="return confirm('are you sure delete this item ?')"><i class="fa fa-trash"></i> Delete</a></li>
							  		@endif
							  	</ul>
							</div>
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
           	$('#tableClients').dataTable();
        });
	</script>
@endpush