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
    <h3>{{ $clients->client_name }}</h3>
    <ul class="nav nav-tabs tabs-bordered">
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
    </ul>
    <div class="tab-content">
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
    </div>
@stop

@push('scripts')
	<script type="text/javascript">
		
	</script>
@endpush