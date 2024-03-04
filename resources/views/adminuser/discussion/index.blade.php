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
    <style type="text/css">
        .tableGlobal td {
			vertical-align: middle;
		}

		.tableGlobal{
		    border-collapse: separate;
		    border:1px solid #D0D5DD;
		    border-radius: 7px;
		    width:100%
		}

		.tableGlobal th {
		    padding: 10px 0px 10px 10px;
		    border-bottom:1px solid #D0D5DD;
		    font-size:14px;
		    font-weight:600;
		}

		.tableGlobal td  {
		    padding: 8px 0px 8px 10px;
		    border-bottom:1px solid #D0D5DD;
		    font-size:13.5px;
		    color:black;
		}

		.tableGlobal tbody tr:last-child td{
		    border-bottom: none;
		}

		.tableGlobal tbody tr:hover {
		    background-color: #f0f0f0;
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

        .modal-content {
            padding: 0px !important;
            -webkit-border-radius: 0px !important;
		    -moz-border-radius: 0px !important;
		    border-radius: 10px !important; 
        }

        .modal-body {
            padding: 25px !important;
        }

        .custom-modal-header {
            padding: 5px;
            width: 95%;
            margin: 0 auto;
            margin-top: 13px;
        }

        .custom-form input {
            border-radius: 7px;
        }

        .custom-form select {
            border-radius: 7px;
        }

        .nav-custom li > a {
            text-transform: capitalize;
        }
    </style>

    <div class="pull-left">
		<h3 style="color:black;font-size:28px;">Q & A</h3>
	</div>
	<div class="pull-right" style="margin-bottom: 24px;">
		<a href="#modal-add-discussion" data-toggle="modal" class="btn btn-md btn-primary" style="border-radius: 9px;"><image src="{{ url('template/images/icon_menu/add.png') }}" width="24" height="24"> Create Discussion</a>
	</div><div style="clear: both;"></div>
    <div>
        <ul class="nav nav-tabs tabs-bordered nav-custom">
            <li class="active">
                <a href="#dashboard" data-toggle="tab" aria-expanded="true">Dashboard</a>
            </li>
            <li class="">
                <a href="#all" data-toggle="tab" aria-expanded="false">All Qustions</a>
            </li>
            <li class="">
                <a href="#high" data-toggle="tab" aria-expanded="false">High Priority</a>
            </li>
            <li class="">
                <a href="#submitted" data-toggle="tab" aria-expanded="false">Submitted</a>
            </li>
            <li class="">
                <a href="#replied" data-toggle="tab" aria-expanded="false">Replied <span class="badge badge-danger">10</span></a>
            </li>
            <li class="">
                <a href="#unanswered" data-toggle="tab" aria-expanded="false">Unanwered <span class="badge badge-warning">3</span></a>
            </li>
            <li class="">
                <a href="#closed" data-toggle="tab" aria-expanded="false">Closed <span class="badge badge-success">23</span></a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="dashboard">
                @include('adminuser.discussion.tabs.dashboard')
            </div>
            <div class="tab-pane" id="all">
                @include('adminuser.discussion.tabs.all_questions')
            </div>
            <div class="tab-pane" id="high">
                @include('adminuser.discussion.tabs.high_questions')
            </div>
            <div class="tab-pane" id="submitted">
                @include('adminuser.discussion.tabs.submitted')
            </div>
            <div class="tab-pane" id="replied">
                @include('adminuser.discussion.tabs.replied')
            </div>
            <div class="tab-pane" id="unanswered">
                @include('adminuser.discussion.tabs.unanswered')
            </div>
            <div class="tab-pane" id="closed">
                @include('adminuser.discussion.tabs.closed')
            </div>
        </div>
    </div>
    @include('adminuser.discussion.create_discussion')
@endsection
@push('scripts')
	<script type="text/javascript">
        function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();

        $(document).ready(function () {
            $('.tableGlobal').dataTable({
                "bPaginate": true,
                "bInfo": false,
                "bSort": true,
                "dom": 'rtip',
                "stripeClasses": false,
            });
        });
    </script>
@endpush