@extends('layouts.app_backend')

@section('content')
	<script type="text/javascript">
		var title = document.getElementById('title');
		@if(!empty($clients->id))
			title.textContent = "Edit Client";
		@else
			title.textContent = "Add Client";
		@endif
	</script>
	<form action="{{ route('backend.client.save') }}" method="POST">
		@csrf
		<div class="row">
			<div class="col-md-6">
				<input type="hidden" name="id" id="id" value="{{ !empty($clients->id) ? $clients->id : '' }}">
				<div class="form-group">
					<label>Client Name <span class="text-danger">*</span></label>
					<input required type="text" name="client_name" id="client_name" value="{{ !empty($clients->client_name) ? $clients->client_name : '' }}" class="form-control">
				</div>
				<div class="form-group">
					<label>Email <span class="text-danger">*</span></label>
					<input required type="email" name="client_email" id="client_email" value="{{ !empty($clients->client_email) ? $clients->client_email : '' }}" class="form-control" placeholder="ex: admin@abc.id">
				</div>
				<div class="form-group">
					<label>Phone <span class="text-danger">*</span></label>
					<input required type="text" name="client_phone" id="client_phone" value="{{ !empty($clients->client_phone) ? $clients->client_phone : '' }}" class="form-control" placeholder="ex: 022-12345">
				</div>
				<div class="form-group">
					<label>Address <span class="text-danger">*</span></label>
					<textarea class="form-control" name="client_address" id="client_address" rows="3">{{ !empty($clients->client_address) ? $clients->client_address : '' }}</textarea>
				</div>
				<div class="form-group">
					<label>Pricing <span class="text-danger">*</span></label>
					<select required name="pricing_id" id="pricing_id" class="form-control">
						<option value="">- select pricing -</option>
						@foreach($pricing as $pricings)
							<option value="{{ $pricings->id }}" {{ !empty($clients->pricing_id) && $clients->pricing_id == $pricings->id ? "selected":"" }} >{{ $pricings->pricing_name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Website</label>
					<input type="text" name="client_website" id="client_website" value="{{ !empty($clients->client_website) ? $clients->client_website : '' }}" class="form-control" placeholder="ex: www.abc.com">
				</div>
				<div class="form-group">
					<label>VAT</label>
					<input type="text" name="client_vat" id="client_vat" value="{{ !empty($clients->client_vat) ? $clients->client_vat : '' }}" class="form-control">
				</div>
				<div class="form-group">
					<label>City</label>
					<input type="text" name="client_city" id="client_city" value="{{ !empty($clients->client_city) ? $clients->client_city : '' }}" class="form-control" placeholder="ex: Bogor">
				</div>
				<div class="form-group">
					<label>Province</label>
					<input type="text" name="client_state" id="client_state" value="{{ !empty($clients->client_state) ? $clients->client_state : '' }}" class="form-control">
				</div>
				<div class="form-group">
					<label>Country</label>
					<select name="client_country" id="client_country" class="form-control">
						<option value="">- select country -</option>
						<option value="indonesia" {{ !empty($clients->client_country) && $clients->client_country == "indonesia" ? "selected":"" }}>Indonesia</option>
					</select>
				</div>
			</div>
			<div class="col-md-12">
				<div class="pull-right">
					<a href="{{ route('backend.client.list') }}" class="btn btn-lg btn-rounded btn-default"><i class="fa fa-arrow-left"></i> Back</a>&nbsp;
					<button class="btn btn-primary btn-lg btn-rounded">
						<i class="fa fa-check"></i> Submit
					</button>
				</div>
			</div>
		</div>
	</form>
@stop