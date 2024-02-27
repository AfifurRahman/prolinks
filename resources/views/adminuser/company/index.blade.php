@extends('layouts.app_client')

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
		title.textContent = "List Companies";
	</script>
	<style type="text/css">
		.tableCompany td {
			vertical-align: middle;
		}

		.tableCompany{
		    border-collapse: separate;
		    border:1px solid #F1F1F1;
		    border-radius: 7px;
		    width:100%
		}

		.tableCompany th {
		    padding: 15px 0px 15px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:14px;
		    font-weight:600;
		}

		.tableCompany td  {
		    padding: 13px 0px 13px 10px;
		    border-bottom:1px solid #F1F1F1;
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
	</style>
	<div class="pull-right" style="margin-bottom: 24px;">
		<a href="#modal-add-company" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-plus-circle"></i> Create Company</a>
	</div><div style="clear: both;"></div>
	@if(count($company) > 0)
		<table id="tableCompany" class="tableCompany">
			<thead>
				<tr style="background-color: #F8F8F8;">
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