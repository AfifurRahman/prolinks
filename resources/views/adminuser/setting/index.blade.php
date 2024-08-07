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
    <style>
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
    <div class="pull-left">
		<h3 style="color:black;font-size:28px;">Settings</h3>
	</div><div style="clear:both;"></div>
    <ul class="nav nav-tabs tabs-bordered">
		<li class="{{ ((!empty(request()->input('tab')) && request()->input('tab') == "account_setting") || (!empty(request()->input('tab')) && request()->input('tab') == "edit_account")) ? "active":"" }}">
            <a href="?tab=account_setting" aria-expanded="false">
                <span class="visible-xs"><i class="fa fa-home"></i></span>
                <span class="hidden-xs">Account Settings</span>
            </a>
        </li>
		<li class="{{ !empty(request()->input('tab')) && request()->input('tab') == "watermark_setting" ? "active":"" }}">
            <a href="?tab=watermark_setting" aria-expanded="false">
                <span class="visible-xs"><i class="fa fa-home"></i></span>
                <span class="hidden-xs">Watermarks</span>
            </a>
        </li>
		<!-- <li class="{{ !empty(request()->input('tab')) && request()->input('tab') == "security_setting" ? "active":"" }}">
            <a href="?tab=security_setting" aria-expanded="false">
                <span class="visible-xs"><i class="fa fa-home"></i></span>
                <span class="hidden-xs">Security</span>
            </a>
        </li> -->
        <li class="{{ !empty(request()->input('tab')) && request()->input('tab') == "email_setting" ? "active":"" }}">
            <a href="?tab=email_setting" aria-expanded="false">
                <span class="visible-xs"><i class="fa fa-home"></i></span>
                <span class="hidden-xs">Preferences</span>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" style="width:100%;">
            @if(!empty(request()->input('tab')) && request()->input('tab') == "email_setting" )
                @include('adminuser.setting.tab.setting_email')
            @elseif(!empty(request()->input('tab')) && request()->input('tab') == "watermark_setting")
                @include('adminuser.setting.tab.setting_watermark')
            @elseif(!empty(request()->input('tab')) && request()->input('tab') == "account_setting")
                @include('adminuser.setting.tab.setting_account')
            @elseif(!empty(request()->input('tab')) && request()->input('tab') == "2fa_setup")
                @include('adminuser.setting.tab.enable_2fa')
            @elseif(!empty(request()->input('tab')) && request()->input('tab') == "edit_account")
                @include('adminuser.setting.tab.edit_account')
            @elseif(!empty(request()->input('tab')) && request()->input('tab') == "security_setting")
                @include('adminuser.setting.tab.setting_security')
            @endif
        </div>
    </div>
	
  
	
@stop

@push('scripts')
    <script src="{{ url('backend/plugins/switchery/switchery.min.js') }}"></script>
	<script type="text/javascript">
		function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();
	</script>
@endpush