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
	<div class="title-tab">
		<h3 style="margin-bottom:32px;">Activities</h3>
	</div>
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
		            	<h3>{{ $total_documents }}</h3>
		            	<p>Total documents</p>
		            </div> <div style="clear: both;"></div>
	            </div>
	        </div>
	        <div class="col-lg-3 col-md-4 col-sm-6">
	            <div class="card-box widget-prolinks-custom">
	                <img src="{{ url('template/images/activities/users.png') }}">
	            	<div class="info-widget">
		            	<h3>{{ $total_users }}</h3>
		            	<p>Total users</p>
		            </div> <div style="clear: both;"></div>
	            </div>
	        </div>
	        <div class="col-lg-3 col-md-4 col-sm-6">
	            <div class="card-box widget-prolinks-custom">
	                <img src="{{ url('template/images/activities/questions.png') }}">
	            	<div class="info-widget">
		            	<h3>{{ $total_qna }}</h3> 
		            	<p>Total questions</p>
		            </div> <div style="clear: both;"></div>
	            </div>
	        </div>
	        <div class="col-lg-3 col-md-4 col-sm-6">
	            <div class="card-box widget-prolinks-custom">
	                <img src="{{ url('template/images/activities/doc_size.png') }}">
	            	<div class="info-widget">
		            	<h3 style="font-size:12px;">{{ $total_size }} <br> ({{App\Helpers\GlobalHelper::formatBytes((DB::table('pricing')->where('id', DB::table('clients')->where('client_id',\globals::get_client_id())->value('pricing_id'))->value('allocation_size')) - (DB::table('upload_files')->where('client_id', \globals::get_client_id())->sum('size')))}} free)</h3>
		            	<p>Total document size</p>
		            </div> <div style="clear: both;"></div>
	            </div>
	        </div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card-box">
					<div class="resume-viewed">
						<h3>Unique visits per day<br><br></h3>
						<div id="container"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="card-box">
					<div class="resume-viewed">
						<h3>Most viewed documents</h3>
						@if(count($most_viewed_doc) == 0)
							<div class="card-box1">
								<center>
									<img src="{{ url('template/images/empty_qna.png') }}" width="300" />
								</center>    
							</div>
						@else
							<table class="table table-hover borderless">
								<thead>
									<tr>
										<th>Filename</th>
										<th>Unique view</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($most_viewed_doc as $doc)
										<tr>
											<td><img class="icon-img" src="{{ url('template/images/ext-file.png') }}"> {!! Str::limit($doc->document_name, 40) !!}</td>
											<td align="center">{{ $doc->total }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						@endif
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card-box">
					<div class="resume-viewed">
						<h3>Most active users</h3>
						@if(count($most_active_user) == 0)
							<div class="card-box1">
								<center>
									<img src="{{ url('template/images/empty_qna.png') }}" width="300" />
								</center>    
							</div>
						@else
							<table class="table table-hover borderless">
								<thead>
									<tr>
										<th>User name</th>
										<th>Docs. accessed</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($most_active_user as $active_user)
										<tr>
											<td>
												{!! \globals::get_user_avatar_small(!empty($active_user->name) ? $active_user->name : $active_user->email_address, !empty($active_user->avatar_color) ? $active_user->avatar_color : '#000') !!} {!! Str::limit( $active_user->name, 40) !!}
											</td>
											<td align="center">{{ $active_user->total }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						@endif
					</div>
				</div>
			</div>
		</div>	
	</div>
@endsection

@push('scripts')
	<script src="{{ url('template/js/highcharts.js') }}"></script>
	<script type="text/javascript">
		function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();
        
		var userData = [{{ DB::table("log_activity")->whereDate("created_at", \Carbon\Carbon::now()->subDays(6)->toDateString())->where("url", url("/login"))->where("response", '"success"')->where("client_id", Auth::user()->client_id)->distinct('user_id')->count('user_id') }}, 
						{{ DB::table("log_activity")->whereDate("created_at", \Carbon\Carbon::now()->subDays(5)->toDateString())->where("url", url("/login"))->where("response", '"success"')->where("client_id", Auth::user()->client_id)->distinct('user_id')->count('user_id') }}, 
						{{ DB::table("log_activity")->whereDate("created_at", \Carbon\Carbon::now()->subDays(4)->toDateString())->where("url", url("/login"))->where("response", '"success"')->where("client_id", Auth::user()->client_id)->distinct('user_id')->count('user_id') }}, 
						{{ DB::table("log_activity")->whereDate("created_at", \Carbon\Carbon::now()->subDays(3)->toDateString())->where("url", url("/login"))->where("response", '"success"')->where("client_id", Auth::user()->client_id)->distinct('user_id')->count('user_id') }}, 
						{{ DB::table("log_activity")->whereDate("created_at", \Carbon\Carbon::now()->subDays(2)->toDateString())->where("url", url("/login"))->where("response", '"success"')->where("client_id", Auth::user()->client_id)->distinct('user_id')->count('user_id') }}, 
						{{ DB::table("log_activity")->whereDate("created_at", \Carbon\Carbon::now()->subDays(1)->toDateString())->where("url", url("/login"))->where("response", '"success"')->where("client_id", Auth::user()->client_id)->distinct('user_id')->count('user_id') }}, 
						{{ DB::table("log_activity")->whereDate("created_at", \Carbon\Carbon::today())->where("url", url("/login"))->where("response", '"success"')->where("client_id", Auth::user()->client_id)->distinct('user_id')->count('user_id') }}];
		    Highcharts.chart('container', {
		        title: {
		            text: ''
		        },
		        xAxis: {
		            categories: ['{{ \Carbon\Carbon::now()->subDays(6)->dayName }}', '{{ \Carbon\Carbon::now()->subDays(5)->dayName }}', '{{ \Carbon\Carbon::now()->subDays(4)->dayName }}', '{{ \Carbon\Carbon::now()->subDays(3)->dayName }}', '{{ \Carbon\Carbon::now()->subDays(2)->dayName }}', '{{ \Carbon\Carbon::now()->subDays(1)->dayName }}', '{{ \Carbon\Carbon::now()->dayName }}']
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