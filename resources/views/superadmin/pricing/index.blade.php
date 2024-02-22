@extends('layouts.app_backend')

@section('navigations')
	@if(\role::get_permission(array('add-pricing')))
		<a href="#modal-add-pricing" data-toggle="modal" class="btn btn-rounded btn-lg btn-primary"><i class="fa fa-plus-circle"></i> Add Pricing</a>
	@endif
	<a href="{{ route('backend.pricing.list') }}" data-toggle="tooltip" data-placement="bottom" title="reload page" class="btn btn-rounded btn-lg btn-success"><i class="fa fa-refresh"></i></a>
@endsection
@section('content')
	<script type="text/javascript">
		var title = document.getElementById('title');
		title.textContent = "Pricing";
	</script>
	<style type="text/css">
		#tablePricing td {
			vertical-align: middle;
		}

		#tablePricing{
		    border-collapse: separate;
		    border:1px solid #F1F1F1;
		    border-radius: 7px;
		    width:100%
		}

		#tablePricing th {
		    padding: 15px 0px 15px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:14px;
		    font-weight:600;
		}

		#tablePricing td  {
		    padding: 13px 0px 13px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:13.5px;
		    color:black;
		}

		#tablePricing tbody tr:last-child td{
		    border-bottom: none;
		}

		#tablePricing tbody tr:hover {
		    background-color: #f0f0f0;
		}

		.btn-cstm:hover{
			background-color: transparent;
		}

		.modal-content {
		    -webkit-border-radius: 0px !important;
		    -moz-border-radius: 0px !important;
		    border-radius: 10px !important; 
		}
	</style>
	<table id="tablePricing">
		<thead>
			<tr style="background-color: #F5F5F5;">
				<th>No</th>
				<th>Pricing Name</th>
				<th>Pricing Type</th>
				<th>Duration</th>
				<th>Allocation Size</th>
				<th>Status</th>
				<th>Action</th>
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
						<td>{{ \globals::formatBytes($pricings->allocation_size) }}</td>
						<td>{!! \globals::label_status($pricings->pricing_status) !!}</td>
						<td width="120">
							<div class="dropdown">
								<button class="btn btn-md dropdown-toggle" type="button" data-toggle="dropdown" style="border: solid 0px; background: transparent;">
									<i class="fa fa-ellipsis-v"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-top">
									@if(\role::get_permission(array('edit-pricing')))
								    	<li><a href="#modal-add-pricing" data-title="Edit Pricing" data-query="{{ $pricings }}" data-size="{{ \globals::formatBytes2($pricings->allocation_size) }}" onclick="getDetail(this)" data-toggle="modal"><i class="fa fa-edit"></i> Edit</a></li>
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
							<select required name="pricing_type" id="pricing_type" onclick="filterType(this)" class="form-control">
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
						<div id="filterAllocation" class="row" style="margin-bottom: 15px; display: none;">
							<div class="col-md-9">
								<label>Allocation Size <span class="text-danger">*</span></label>
								<input type="text" name="allocation_size" id="allocation_size" class="form-control">
								<div style="border: solid 2px #CCC; border-radius: 10px; margin-top: 5px; padding: 5px;">
									<small class="text-danger">
										Guide : <br>
										1 GB = 1,024 MB
									</small>
								</div>
							</div>
							<div class="col-md-3">
								<label>&nbsp;</label>
								<select name="size_type" id="size_type" class="form-control">
									<option value="MB">MB</option>
									<option value="GB">GB</option>
								</select>
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
	                    <button type="button" onclick="reloadPage()" class="btn btn-rounded btn-default waves-effect" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
	                    <button type="submit" class="btn btn-primary btn-rounded waves-effect waves-light"><i class="fa fa-check"></i> Submit</button>
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
        	var allocation_sizes = $(element).data('size');
        	
        	$("#titleModal").html(title);
        	
        	$("#id").val(query.id);
        	$("#pricing_type").val(query.pricing_type).trigger('change');
    		$("#pricing_name").val(query.pricing_name);
    		$("#allocation_size").val(allocation_sizes);
    		$("#size_type").val(query.size_type).trigger('change');
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