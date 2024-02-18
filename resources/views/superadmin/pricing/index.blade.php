@extends('layouts.app_backend')

@section('content')
	<style type="text/css">
		#tablePricing td {
			vertical-align: middle;
		}
	</style>
	<div class="card-box">
		@if(\role::get_permission(array('add-pricing')))
			<a href="#modal-add-pricing" data-toggle="modal" class="btn btn-primary"><i class="fa fa-plus"></i> Add Pricing</a>
		@endif
		<a href="{{ route('backend.pricing.list') }}" data-toggle="tooltip" title="reload page" class="btn btn-success"><i class="fa fa-refresh"></i></a><br><br>
		<table id="tablePricing" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>No</th>
					<th>Pricing Name</th>
					<th>Pricing Type</th>
					<th>Duration</th>
					<th>Allocation Size</th>
					<th>Status</th>
					<th>#</th>
				</tr>
			</thead>
			<tbody>
				@if(count($pricing) > 0)
					@foreach($pricing as $key => $pricings)
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $pricings->pricing_name }}</td>
							<td>{!! \globals::label_type_pricing($pricings->pricing_type) !!}</td>
							<td>{{ !empty($pricings->duration) ? $pricings->duration.' Month' : '-' }}</td>
							<td>{{ $pricings->allocation_size }}</td>
							<td>{!! \globals::label_status($pricings->pricing_status) !!}</td>
							<td>
								<div class="dropdown">
									<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Action
									<span class="caret"></span></button>
									<ul class="dropdown-menu">
										@if(\role::get_permission(array('edit-pricing')))
									    	<li><a href="#modal-add-pricing" data-title="Edit Pricing" data-query="{{ $pricings }}" onclick="getDetail(this)" data-toggle="modal"><i class="fa fa-edit"></i> Edit</a></li>
									    @endif

									    @if(\role::get_permission(array('delete-pricing')))
									    	<li><a href="{{ route('backend.pricing.delete', $pricings->pricing_id) }}" onclick="return confirm('are you sure delete this item ?')"><i class="fa fa-trash"></i> Delete</a></li>
								  		@endif
								  	</ul>
								</div>
							</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
	</div>

	<div id="modal-add-pricing" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" onclick="reloadPage()" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="titleModal">Add Pricing</h4>
                </div>
                <div class="modal-body">
                	<form action="{{ route('backend.pricing.save') }}" method="POST">
						@csrf
						<input type="hidden" name="id" id="id">
						<div class="form-group">
							<label>Pricing Type</label>
							<select name="pricing_type" id="pricing_type" onclick="filterType(this)" class="form-control">
								<option value="">- select pricing type -</option>
								@foreach(\globals::get_type_pricing() as $type)
									<option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group" id="filterPricingName" style="display: none;">
							<label>Pricing Name <span class="text-danger">*</span></label>
							<input required type="text" name="pricing_name" id="pricing_name" class="form-control">
						</div>
						<div class="form-group" id="filterAllocation" style="display: none;">
							<label>Allocation Size <span class="text-danger">*</span></label>
							<div class="input-group">
								<input type="text" name="allocation_size" id="allocation_size" class="form-control">
								<span class="input-group-addon">MB</span>
							</div>
						</div>
						<div class="form-group" id="filterDuration" style="display: none;">
							<label>Duration <span class="text-danger">*</span></label>
							<div class="input-group">
								<input type="text" name="duration" id="duration" class="form-control">
								<span class="input-group-addon">Month</span>
							</div>
						</div>
						<div class="form-group">
							<label>Pricing Desc</label>
							<textarea name="pricing_desc" id="pricing_desc" class="form-control" rows="3"></textarea>
						</div>
                </div>
                <div class="modal-footer">
	                    <button type="button" onclick="reloadPage()" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
	                    <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fa fa-check"></i> Submit</button>
                	</form>
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
	<script type="text/javascript">
		$(document).ready(function () {
           	$('#tablePricing').dataTable();
        });

        function filterType(element) {
        	if (element.value == 1) {
        		$("#filterPricingName").css("display", "block");
        		$("#filterAllocation").css("display", "block");
        		$("#filterDuration").css("display", "none");
        		
        		$("#allocation_size").prop("required", true);
        		$("#duration").prop("required", false);
        	}else if(element.value == 2){
        		$("#filterPricingName").css("display", "block");
        		$("#filterAllocation").css("display", "block");
        		$("#filterDuration").css("display", "block");

        		$("#allocation_size").prop("required", true);
        		$("#duration").prop("required", true);
        	}else{
        		$("#filterPricingName").css("display", "none");
        		$("#filterAllocation").css("display", "none");
        		$("#filterDuration").css("display", "none");

        		$("#allocation_size").prop("required", false);
        		$("#duration").prop("required", false);
        	}
        }

        function getDetail(element) {
        	var title = $(element).data('title');
        	var query = $(element).data('query');

        	$("#titleModal").html(title);
        	
        	$("#id").val(query.id);
        	$("#pricing_type").val(query.pricing_type).trigger('change');
	    		$("#pricing_name").val(query.pricing_name);
	    		$("#allocation_size").val(query.allocation_size);
	    		$("#duration").val(query.duration);
	    		$("#pricing_desc").val(query.pricing_desc);

        	if (query.pricing_type == 1) {
        		$("#filterPricingName").css("display", "block");
        		$("#filterAllocation").css("display", "block");
        		$("#filterDuration").css("display", "none");
        		
        		$("#allocation_size").prop("required", true);
        		$("#duration").prop("required", false);
        	}else if(query.pricing_type == 2){
        		$("#filterPricingName").css("display", "block");
        		$("#filterAllocation").css("display", "block");
        		$("#filterDuration").css("display", "block");

        		$("#allocation_size").prop("required", true);
        		$("#duration").prop("required", true);
        	}
        }

        function reloadPage() {
        	location.reload();
        }
	</script>
@endpush