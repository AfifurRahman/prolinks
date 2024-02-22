@extends('layouts.app_backend')

@section('content')
	<style type="text/css">
		.header-info-client {
			margin-bottom: 24px;
			line-height: 5px;
		}
	</style>
	<div class="header-info-client">
        <a href="{{ route('backend.monitoring.list') }}" class="btn btn-default btn-rounded"><i class="fa fa-arrow-left"></i> Back</a>
        <div class="pull-right">
            <select name="clients_name" class="form-control" style="width: 350px;">
                @foreach($client as $optionsClient)
                    <option value="{{ $optionsClient->client_id }}" {{ !empty($clients->client_id) && $clients->client_id == $optionsClient->client_id ? "selected": "" }}>{{ $optionsClient->client_name }}</option>
                @endforeach
            </select>
        </div><div style="clear: both;"></div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="card-box widget-box-two widget-two-primary">
                <i class="mdi mdi-chart-areaspline widget-two-icon"></i>
                <div class="wigdet-two-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Statistics">Statistics</p>
                    <h2><span data-plugin="counterup">34578</span> <small><i class="mdi mdi-arrow-up text-success"></i></small></h2>
                    <p class="text-muted m-0"><b>Last:</b> 30.4k</p>
                </div>
            </div>
            <div class="card-box widget-box-two widget-two-warning">
                <i class="mdi mdi-layers widget-two-icon"></i>
                <div class="wigdet-two-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="User This Month">User This Month</p>
                    <h2><span data-plugin="counterup">52410 </span> <small><i class="mdi mdi-arrow-up text-success"></i></small></h2>
                    <p class="text-muted m-0"><b>Last:</b> 40.33k</p>
                </div>
            </div>
            <div class="card-box widget-box-two widget-two-danger">
                <i class="mdi mdi-access-point-network widget-two-icon"></i>
                <div class="wigdet-two-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Statistics">Statistics</p>
                    <h2><span data-plugin="counterup">6352</span> <small><i class="mdi mdi-arrow-up text-success"></i></small></h2>
                    <p class="text-muted m-0"><b>Last:</b> 30.4k</p>
                </div>
            </div>
            <div class="card-box widget-box-two widget-two-success">
                <i class="mdi mdi-account-convert widget-two-icon"></i>
                <div class="wigdet-two-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="User Today">User Today</p>
                    <h2><span data-plugin="counterup">895 </span> <small><i class="mdi mdi-arrow-down text-danger"></i></small></h2>
                    <p class="text-muted m-0"><b>Last:</b> 1250</p>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card-box">
                <h4 class="header-title m-t-0 m-b-30">Total Revenue</h4>

                <div id="website-stats" style="height: 320px;" class="flot-chart"></div>
            </div>
            <div class="card-box">
                <h4 class="header-title m-t-0">Sales Analytics</h4>

                <div class="pull-right m-b-30">
                    <div id="reportrange" class="form-control">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                        <span></span>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div id="donut-chart">
                    <div id="donut-chart-container" class="flot-chart" style="height: 240px;">
                    </div>
                </div>

                <p class="text-muted m-b-0 m-t-15 font-13 text-overflow">Pie chart is used to see the proprotion of each data groups, making Flot pie chart is pretty simple, in order to make pie chart you have to incldue jquery.flot.pie.js plugin.</p>
            </div>
        </div>
    </div>
@stop

@push('scripts')
	<script src="{{ url('template/plugins/flot-chart/jquery.flot.min.js') }}"></script>
    <script src="{{ url('template/plugins/flot-chart/jquery.flot.time.js') }}"></script>
    <script src="{{ url('template/plugins/flot-chart/jquery.flot.tooltip.min.js') }}"></script>
    <script src="{{ url('template/plugins/flot-chart/jquery.flot.resize.js') }}"></script>
    <script src="{{ url('template/plugins/flot-chart/jquery.flot.pie.js') }}"></script>
    <script src="{{ url('template/plugins/flot-chart/jquery.flot.selection.js') }}"></script>
    <script src="{{ url('template/plugins/flot-chart/jquery.flot.crosshair.js') }}"></script>
	<script src="{{ url('template/js/jquery.dashboard_2.js') }}"></script>
	<script type="text/javascript">
		
	</script>
@endpush