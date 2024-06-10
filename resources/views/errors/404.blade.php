@extends('layouts.app_client')

@section('navigationbar')
@endsection

@section('content')
    <div class="card-box">
        <center>
            <h1>Not Found</h1>
            <img src="{{ url('template/images/not-found.png') }}" width="200" height="200" /><br><br>
            <p>It is a long established fact that a reader will be distracted by the readable content of a<br> page when looking at its layout.</p>
        </center>    
    </div>
@endsection