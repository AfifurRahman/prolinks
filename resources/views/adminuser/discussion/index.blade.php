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
		<h3 style="color:black;font-size:28px;">Questions and answers</h3>
	</div>
	<div class="pull-right" style="margin-bottom: 24px; margin-top:5px;">
        <a href="" class="btn btn-md btn-default" style="border-radius: 9px; color:#1570EF; font-weight:bold;"> Export All</a>
        <a href="" class="btn btn-md btn-default" style="border-radius: 9px; color:#1570EF; font-weight:bold;"><image src="{{ url('template/images/icon_menu/broadcast.png') }}" width="22" height="22"> Create FAQ</a>
		<a href="#modal-add-discussion" data-toggle="modal" class="btn btn-md btn-primary" style="border-radius: 9px;">Ask a questions</a>
	</div><div style="clear: both;"></div>
    <div>
        <ul class="nav nav-tabs tabs-bordered nav-custom">
            <li class="active">
                <a href="#all" data-toggle="tab" aria-expanded="true">All</a>
            </li>
            <li class="">
                <a href="#unanswered" data-toggle="tab" aria-expanded="false">Unanswered</a>
            </li>
            <li class="">
                <a href="#answered" data-toggle="tab" aria-expanded="false">Answered</a>
            </li>
            <li class="">
                <a href="#closed" data-toggle="tab" aria-expanded="false">Closed</a>
            </li>
            <li class="">
                <a href="#faq" data-toggle="tab" aria-expanded="false">FAQ</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="all">
                @include('adminuser.discussion.tabs.all_questions')
            </div>
            <div class="tab-pane" id="unanswered">
                @include('adminuser.discussion.tabs.unanswered')
            </div>
            <div class="tab-pane" id="answered">
                @include('adminuser.discussion.tabs.answered')
            </div>
            <div class="tab-pane" id="closed">
                @include('adminuser.discussion.tabs.closed')
            </div>
            <div class="tab-pane" id="faq">
                @include('adminuser.discussion.tabs.faq')
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