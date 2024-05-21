@extends('layouts.app_backend')

@section('content')
    <script type="text/javascript">
		var title = document.getElementById('title');
		title.textContent = "Monitoring Client";
	</script>
	<style type="text/css">
		.header-info-client {
			margin-bottom: 24px;
			line-height: 5px;
		}
	</style>
	<div class="header-info-client">
        <a href="{{ route('backend.monitoring.list') }}" class="btn btn-default btn-rounded"><i class="fa fa-arrow-left"></i> Back</a>
        <div class="pull-right">
            <select name="clients_name" id="clients_name" onchange="changeClient(this)" class="form-control select2">
                @foreach($users as $usersdata)
                    <optgroup label="{{ $usersdata->client_name }}">
                        @if(count($usersdata->RefClientUser) > 0)
                            @foreach($usersdata->RefClientUser as $dtl)
                                @php
                                    $roles = "";
                                    if($dtl->role == 0){
                                        $roles = "administrator";
                                    }elseif($dtl->role == 1){
                                        $roles = "collaborator";
                                    }elseif($dtl->role == 2){
                                        $roles = "reviewer";
                                    }
                                @endphp
                                <option value="{{ base64_encode($dtl->id) }}" {{ !empty($user->id) && $user->id == $dtl->id ? "selected": "" }}>{{ !empty($dtl->name) ? $dtl->name : $dtl->email_address }} - <span class="text-muted">{{ $roles }}</span></option>
                            @endforeach
                        @endif
                    </optgroup>
                @endforeach
            </select>
        </div><div style="clear: both;"></div>
    </div>
    <div style="margin-bottom:20px;">
        <h3>{{ !empty($user->name) ? $user->name : $user->email_address }}</h3>
        <h4 class="text-muted" style="margin-bottom: 0;">{{ !empty($user->RefClient->client_name) ? $user->RefClient->client_name : '' }}</h4>
        <p class="text-muted" style="margin-bottom: 0;">{{ $user->email_address }}</p>
        <p class="text-muted" style="margin-bottom: 0;">
            @if($user->role == 0) 
                <span class="label label-default" style="background-color: #D7D7D7; border-radius: 10px; color:#000;">Administrator</span>
            @elseif($user->role == 1)
                <span class="label label-default" style="background-color: #D7D7D7; border-radius: 10px; color:#000;">Collaborator</span>
            @elseif($user->role == 2)
                <span class="label label-default" style="background-color: #D7D7D7; border-radius: 10px; color:#000;">Reviewer</span>
            @endif
        </p>
    </div>
    <ul class="nav nav-tabs">
        @if($user->role == 0)
            <li class="{{ !empty(request()->input('tab')) && request()->input('tab') == "dashboard" ? "active":"" }}">
                <a href="?tab=dashboard" aria-expanded="false">
                    <span class="visible-xs"><i class="fa fa-home"></i></span>
                    <span class="hidden-xs">Dashboard</span>
                </a>
            </li>
            <li class="{{ !empty(request()->input('tab')) && request()->input('tab') == "users" ? "active":"" }}">
                <a href="?tab=users" aria-expanded="true">
                    <span class="visible-xs"><i class="fa fa-user"></i></span>
                    <span class="hidden-xs">Access User</span>
                </a>
            </li>
            <li class="{{ !empty(request()->input('tab')) && request()->input('tab') == "project" ? "active":"" }}">
                <a href="?tab=project" aria-expanded="false">
                    <span class="visible-xs"><i class="fa fa-envelope-o"></i></span>
                    <span class="hidden-xs">Project</span>
                </a>
            </li>
            <li class="{{ !empty(request()->input('tab')) && request()->input('tab') == "qna" ? "active":"" }}">
                <a href="?tab=qna" aria-expanded="false">
                    <span class="visible-xs"><i class="fa fa-cog"></i></span>
                    <span class="hidden-xs">Questions & Answer</span>
                </a>
            </li>
        @else
            <li class="{{ !empty(request()->input('tab')) && request()->input('tab') == "documents" ? "active":"" }}">
                <a href="?tab=documents" aria-expanded="false">
                    <span class="visible-xs"><i class="fa fa-envelope-o"></i></span>
                    <span class="hidden-xs">Documents</span>
                </a>
            </li>
            <li class="{{ !empty(request()->input('tab')) && request()->input('tab') == "qna" ? "active":"" }}">
                <a href="?tab=qna" aria-expanded="false">
                    <span class="visible-xs"><i class="fa fa-cog"></i></span>
                    <span class="hidden-xs">Questions & Answer</span>
                </a>
            </li>
        @endif
    </ul>
    <div class="tab-content">
        @if($user->role == 0)
            <div class="tab-pane {{ !empty(request()->input('tab')) && request()->input('tab') == "dashboard" ? "active":"" }}" style="width:100%;">
                @include('superadmin.monitoring_client.acces_client.dashboard')
            </div>
            <div class="tab-pane {{ !empty(request()->input('tab')) && request()->input('tab') == "users" ? "active":"" }}">
                @include('superadmin.monitoring_client.acces_client.users')
            </div>
            <div class="tab-pane {{ !empty(request()->input('tab')) && request()->input('tab') == "project" ? "active":"" }}">
                @include('superadmin.monitoring_client.acces_client.project')
            </div>
            <div class="tab-pane {{ !empty(request()->input('tab')) && request()->input('tab') == "qna" ? "active":"" }}">
                @include('superadmin.monitoring_client.acces_client.qna')
            </div>
        @else
            <div class="tab-pane {{ !empty(request()->input('tab')) && request()->input('tab') == "documents" ? "active":"" }}">
                {{-- HTML @include('superadmin.monitoring_client.acces_client.project') --}}
            </div>
            <div class="tab-pane {{ !empty(request()->input('tab')) && request()->input('tab') == "qna" ? "active":"" }}">
                @include('superadmin.monitoring_client.acces_client.qna')
            </div>
        @endif
    </div>
@stop

@push('scripts')
    <link href="{{ url('template/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ url('template/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ url('template/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
		function changeClient(element) {
            window.location.href = "{{ URL::to('backend/monitoring/detail') }}"+ "/" + element.value + "?tab=" + "{{ !empty(request()->input('tab')) ? request()->input('tab') : 'dashboard' }}";
        }

        $(".select3").select2({
            width: 'resolve'
        });
	</script>
@endpush