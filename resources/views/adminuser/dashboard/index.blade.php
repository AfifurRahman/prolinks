@extends('layouts.app_client')

@section('content')
	<style type="text/css">
		.dashboard-summary {
			margin-top: 24px;
		}

		.widget-prolinks-custom {

		}

		.widget-prolinks-custom img {
			float: left;
			margin-top: 12px;
		}

		.info-widget {
			float: left;
			margin-left: 10px;
		}

		.info-widget h3 {

		}

		.info-widget p {
			line-height: 0px;
		}

		.resume-viewed h3 {
			margin-bottom: 15px;
		}

		.resume-viewed th{
			background-color: #F5F5F5;
			border-radius: 2px;
		}

		.icon-img {
			width: 22px;
			height: 22px;
		}

		.borderless td, .borderless th {
		    border: none !important;
		}
	</style>
	<div class="filter-activities">
		<form class="form-inline" role="form">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</span>
					<select class="form-control">
						<option value="">last 7 days</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<select class="form-control">
					<option value="">all users & group</option>
				</select>
			</div>
		</form>
	</div>

	<div class="dashboard-summary">
		<div class="row">
			<div class="col-lg-3 col-md-4 col-sm-6">
	            <div class="card-box widget-prolinks-custom">
	                <img src="{{ url('template/images/activities/document.png') }}">
	            	<div class="info-widget">
		            	<h3>1.256</h3>
		            	<p>Total documents</p>
		            </div> <div style="clear: both;"></div>
	            </div>
	        </div>
	        <div class="col-lg-3 col-md-4 col-sm-6">
	            <div class="card-box widget-prolinks-custom">
	                <img src="{{ url('template/images/activities/users.png') }}">
	            	<div class="info-widget">
		            	<h3>8</h3>
		            	<p>Total users</p>
		            </div> <div style="clear: both;"></div>
	            </div>
	        </div>
	        <div class="col-lg-3 col-md-4 col-sm-6">
	            <div class="card-box widget-prolinks-custom">
	                <img src="{{ url('template/images/activities/questions.png') }}">
	            	<div class="info-widget">
		            	<h3>128</h3>
		            	<p>Total questions</p>
		            </div> <div style="clear: both;"></div>
	            </div>
	        </div>
	        <div class="col-lg-3 col-md-4 col-sm-6">
	            <div class="card-box widget-prolinks-custom">
	                <img src="{{ url('template/images/activities/doc_size.png') }}">
	            	<div class="info-widget">
		            	<h3>7.21 GB</h3>
		            	<p>Total document size</p>
		            </div> <div style="clear: both;"></div>
	            </div>
	        </div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="card-box">
					<div class="resume-viewed">
						<h3>Most viewed documents</h3>
						<table class="table table-hover borderless">
							<thead>
								<tr>
									<th>Filename</th>
									<th>Unique view</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><img class="icon-img" src="{{ url('template/images/ext-file.png') }}"> Control of Quality and Regulatory...xls</td>
									<td>798</td>
								</tr>
								<tr>
									<td><img class="icon-img" src="{{ url('template/images/ext-file.png') }}"> Facilities Management.doc</td>
									<td>492</td>
								</tr>
								<tr>
									<td><img class="icon-img" src="{{ url('template/images/ext-file.png') }}"> External Audits.pdf</td>
									<td>447</td>
								</tr>
								<tr>
									<td><img class="icon-img" src="{{ url('template/images/ext-img.png') }}"> Employee Training and Developm...img</td>
									<td>274</td>
								</tr>
								<tr>
									<td><img class="icon-img" src="{{ url('template/images/ext-file.png') }}"> Internal Quality Audits.docs</td>
									<td>185</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card-box">
					<div class="resume-viewed">
						<h3>Most active users</h3>
						<table class="table table-hover borderless">
							<thead>
								<tr>
									<th>User name</th>
									<th>Docs. accessed</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><img class="icon-img" src="{{ url('template/images/avatar.png') }}"> Yanuar Adhitia Tutkey</td>
									<td>994</td>
								</tr>
								<tr>
									<td><img class="icon-img" src="{{ url('template/images/avatar.png') }}"> Aryo Agung Benardi</td>
									<td>826</td>
								</tr>
								<tr>
									<td><img class="icon-img" src="{{ url('template/images/avatar.png') }}"> Deny Stefany Febri</td>
									<td>738</td>
								</tr>
								<tr>
									<td><img class="icon-img" src="{{ url('template/images/avatar.png') }}"> Christin Purnama</td>
									<td>600</td>
								</tr>
								<tr>
									<td><img class="icon-img" src="{{ url('template/images/avatar.png') }}"> Rizki Agung Maulana</td>
									<td>274</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card-box">
					<div class="resume-viewed">
						<h3>Unique visits per day</h3>
						<div id="container"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script src="{{ url('template/js/highcharts.js') }}"></script>
	<script type="text/javascript">
		var userData = [100, 90, 100, 80, 200, 50, 60];
		    Highcharts.chart('container', {
		        title: {
		            text: 'Users'
		        },
		        xAxis: {
		            categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
		        },
		        yAxis: {
		            title: {
		                text: 'Number of Users'
		            }
		        },
		        legend: {
		            layout: 'vertical',
		            align: 'right',
		            verticalAlign: 'middle'
		        },
		        plotOptions: {
		            series: {
		                allowPointSelect: true
		            }
		        },
		        series: [{
		        	type: 'areaspline',
		            name: 'Users',
		            data: userData
		        }],
		        responsive: {
		            rules: [{
		                condition: {
		                    maxWidth: 500
		                },
		                chartOptions: {
		                    legend: {
		                        layout: 'horizontal',
		                        align: 'center',
		                        verticalAlign: 'bottom'
		                    }
		                }
		            }]
		        }
		    });
	</script>
@endpush