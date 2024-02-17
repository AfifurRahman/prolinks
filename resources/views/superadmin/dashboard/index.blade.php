@extends('layouts.app_backend')

@section('content')
	<div class="row">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-chart-areaspline widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Statistics">Statistics</p>
                    <h2>34578 <small><i class="mdi mdi-arrow-up text-success"></i></small></h2>
                    <p class="text-muted m-0"><b>Last:</b> 30.4k</p>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-account-convert widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="User Today">User Today</p>
                    <h2>895 <small><i class="mdi mdi-arrow-down text-danger"></i></small></h2>
                    <p class="text-muted m-0"><b>Last:</b> 1250</p>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-layers widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="User This Month">User This Month</p>
                    <h2>52410 <small><i class="mdi mdi-arrow-up text-success"></i></small></h2>
                    <p class="text-muted m-0"><b>Last:</b> 40.33k</p>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-av-timer widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Request Per Minute">Request Per Minute</p>
                    <h2>652 <small><i class="mdi mdi-arrow-down text-danger"></i></small></h2>
                    <p class="text-muted m-0"><b>Last:</b> 956</p>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-account-multiple widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Users">Total Users</p>
                    <h2>3245 <small><i class="mdi mdi-arrow-down text-danger"></i></small></h2>
                    <p class="text-muted m-0"><b>Last:</b> 20k</p>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card-box widget-box-one">
                <i class="mdi mdi-download widget-one-icon"></i>
                <div class="wigdet-one-content">
                    <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="New Downloads">New Downloads</p>
                    <h2>78541 <small><i class="mdi mdi-arrow-up text-success"></i></small></h2>
                    <p class="text-muted m-0"><b>Last:</b> 50k</p>
                </div>
            </div>
        </div><!-- end col -->
    </div>

    <div class="row">
        <div class="col-lg-4">
    		<div class="card-box">
    			<h4 class="header-title m-t-0">Daily Sales</h4>
                <div class="widget-chart text-center">
                    <div id="morris-donut-example"style="height: 245px;"></div>
                    <ul class="list-inline chart-detail-list m-b-0">
                        <li>
                            <h5 class="text-danger"><i class="fa fa-circle m-r-5"></i>Series A</h5>
                        </li>
                        <li>
                            <h5 class="text-success"><i class="fa fa-circle m-r-5"></i>Series B</h5>
                        </li>
                    </ul>
            	</div>
    		</div>
        </div><!-- end col -->
        <div class="col-lg-4">
            <div class="card-box">
                <h4 class="header-title m-t-0">Statistics</h4>
                <div id="morris-bar-example" style="height: 280px;"></div>
            </div>
        </div><!-- end col -->
        <div class="col-lg-4">
            <div class="card-box">
                <h4 class="header-title m-t-0">Total Revenue</h4>
                <div id="morris-line-example" style="height: 280px;"></div>
            </div>
        </div><!-- end col -->
    </div>
@stop