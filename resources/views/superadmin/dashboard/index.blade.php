@extends('layouts.app_backend')

@section('content')
    @php
        $most_active_user = DB::select("SELECT b.name, b.avatar_color, b.email, COUNT(a.user_id) as total FROM log_activity a JOIN users b ON a.user_id = b.user_id where a.description LIKE '%logged in%' GROUP BY a.user_id, b.name, b.avatar_color, b.email ORDER BY total DESC LIMIT 5");
        $most_viewed_doc = DB::select("SELECT document_name,document_id, COUNT(user_id) as total FROM log_view_document GROUP BY user_id, document_name, document_id ORDER BY total DESC LIMIT 5");
            
    @endphp
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

    <script type="text/javascript">
        var title = document.getElementById('title');
        title.textContent = "Dashboard";
    </script>
	<div class="row">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-account-multiple widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Statistics">Total Users</p>
                    <h2>{{ number_format(DB::table('users')->count()) }}</h2>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-layers widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="User Today">Total Documents</p>
                    <h2>{{ number_format(DB::table('upload_files')->count()) }}</h2>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-download widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="User This Month">Total Download Documents</p>
                    <h2>{{ number_format(DB::table('log_activity')->where('url', 'like', '%documents/download%')->count() + DB::table('log_activity')->where('url', 'like', '%documents/select%')->count()) }}</h2>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-account-multiple widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Request Per Minute">Total User Active</p>
                    <h2>{{ number_format(DB::table('users')->where('status', '1')->count()) }}</h2>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-layers widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Users">Total Discussion</p>
                    <h2>{{ number_format(DB::table('discussions')->count()) }}</h2>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-av-timer widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="New Downloads">Total Quota Usage</p>
                    <h2>{{ App\Helpers\GlobalHelper::formatBytes(DB::table('upload_files')->sum('size')) }}</h2>
                </div>
            </div>
        </div><!-- end col -->
    </div>

    <div class="row">
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
												{!! Str::limit( $active_user->name, 40) !!}
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
											<td><a href="{{ route('adminuser.documents.view', base64_encode($doc->document_id)) }}"><img class="icon-img" src="{{ url('template/images/ext-file.png') }}">&nbsp;&nbsp;{!! Str::limit($doc->document_name, 40) !!}</a></td>
											<td align="center">{{ $doc->total }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						@endif
					</div>
				</div>
			</div>
		</div>	
@stop