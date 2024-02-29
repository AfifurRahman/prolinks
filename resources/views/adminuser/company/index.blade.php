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
	<script type="text/javascript">
		var title = document.getElementById('title');
		title.textContent = "";
	</script>
	<style type="text/css">
		.tableCompany td {
			vertical-align: middle;
		}

		.tableCompany{
		    border-collapse: separate;
		    border:1px solid #D0D5DD;
		    border-radius: 7px;
		    width:100%
		}

		.tableCompany th {
		    padding: 15px 0px 15px 10px;
		    border-bottom:1px solid #D0D5DD;
		    font-size:14px;
		    font-weight:600;
		}

		.tableCompany td  {
		    padding: 13px 0px 13px 10px;
		    border-bottom:1px solid #D0D5DD;
		    font-size:13.5px;
		    color:black;
		}

		.tableCompany tbody tr:last-child td{
		    border-bottom: none;
		}

		.tableCompany tbody tr:hover {
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

	    #box_helper{
	        margin-bottom:16px;
	        display:flex;
	        width:100%;
	        justify-content: space-between;
	    }

	    #filter_button{
	        padding:7px 15px 6px 17px;
	        background: #FFFFFF; 
	        color:#546474;
	        border:1px solid #D0D5DD;
	        border-radius:10px;
	    }

	    #filtericon{
	        margin-top:-1px;
	        margin-right:4px;
	        height:23px;
	        width:20px;
	    }

	    #searchbox{
	        width:22%;
	        padding:8px 10px 5px 12px;
	        border:1px solid #CED5DD;
	        border-radius: 8px;
	    }

	    #searchicon{
	        width:19px;
	        height:19px;
	        margin-top:-3px;
	        margin-right:4px;
	    }

	    #search_bar{
	        border:none;
	    }

	    #filter_status {
	    	width: 200px;
	    }

	    .active_status{
	        background: #ECFDF3;
	        font-size:12px;
	        font-weight:600;
	        color: #027A48; 
	        padding:5px 10px 5px 10px;
	        border-radius:25px;
	    }

	    .disabled_status{
	        background: #FEF3F2;
	        font-size:12px;
	        font-weight:600;
	        color: #912018; 
	        padding:5px 10px 5px 10px;
	        border-radius:25px;
	    }
	</style>
	<div class="pull-left">
		<h3 style="color:black;font-size:28px;">Companies</h3>
	</div>
	<div class="pull-right" style="margin-bottom: 24px;">
		<a href="#modal-add-company" data-toggle="modal" class="btn btn-md btn-primary" style="border-radius: 9px;"><image src="{{ url('template/images/icon_menu/add.png') }}" width="24" height="24"> Create Company</a>
	</div><div style="clear: both;"></div>
	<div id="box_helper">
        <div>
            <select name="filter_status" id="filter_status" class="form-control">
            	<option value="">All Status</option>
            	<option value="active">Active</option>
            	<option value="disabled">Disable</option>
            </select>
        </div>
        <div id="searchbox">
            <image id="searchicon" src="{{ url('template/images/icon_menu/search.png') }}"></image>
            <input type="text" id="search_bar" placeholder="Search company...">
        </div>
    </div>
	@if(count($company) > 0)
		<table id="tableCompany" class="tableCompany">
			<thead>
				<tr style="background-color: #F9FAFB;">
					<th></th>
					<th>Company</th>
					<th>Status</th>
					<th>Project</th>
					<th>Users</th>
					<th>Created at</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				@foreach($company as $key => $companies)
					<tr>
						<td width="50" align="center">
							<input type="checkbox" name="checkbox_company" class="form-control">
						</td>
						<td>
							<a href="{{ route('company.detail-company', $companies->company_id) }}">
								<img src="{{ url('template/images/data-company.png') }}" width="24" height="24">&nbsp; {{ $companies->company_name }}
							</a>
						</td>
						<td>{!! \globals::label_status_company($companies->company_status) !!}</td>
						<td>0</td>
						<td>0</td>
						<td>{{ date('d M Y H:i', strtotime($companies->created_at)) }}</td>
						<td width="100">
							<div class="dropdown">
								<button class="btn btn-md dropdown-toggle" type="button" data-toggle="dropdown" style="border: solid 0px; background: transparent;">
									<i class="fa fa-ellipsis-v"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="#modal-add-company" data-toggle="modal" data-title="Edit Companies" data-query="{{ $companies }}" onclick="getDetailCompanies(this)"><i class="fa fa-edit"></i> Edit</a></li>
									<li><a href="{{ route('company.disable-company', $companies->company_id) }}" onclick="return confirm('are you sure disable this company ?')"><i class="fa fa-unlock-alt"></i> Disable</a></li>
								   	<li><a href="{{ route('company.delete-company', $companies->company_id) }}" onclick="return confirm('are you sure delete this item ?')"><i class="fa fa-trash"></i> Delete</a></li>
							  	</ul>
							</div>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@else
		<div class="card-box">
			<center>
				<img src="{{ url('template/images/no-company.png') }}" width="370" height="260">
			</center>
		</div>
	@endif

	@include('adminuser.company._form')
@stop

@push('scripts')
	<script type="text/javascript">
		$(document).ready(function () {
            $('#tableCompany').dataTable({
                "bPaginate": true,
                "bInfo": false,
                "bSort": false,
                "dom": 'rtip',
                "stripeClasses": false,
            });

            $('#search_bar').keyup(function() {
                var table = $('#tableCompany').DataTable();
                table.search($(this).val()).draw();
            });

            $('#filter_status').change(function() {
                var table = $('#tableCompany').DataTable();
                table.search($(this).val()).draw();
            });
        });
	</script>
@endpush