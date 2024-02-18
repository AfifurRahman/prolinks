@extends('layouts.app_backend')

@section('content')
	<div align="center">
		<img src="{{ url('template/images/access_denied.jpg') }}"><br>
		<a href="javascript::void(0)" onclick="history.back()" class="btn btn-lg btn-default">Back To Home <i class="fa fa-arrow-right"></i></a>
	</div>
@stop