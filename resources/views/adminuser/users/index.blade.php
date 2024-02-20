@extends('layouts.app_client')

@section('content')
    <style type="text/css">
		#tablePricing td {
			vertical-align: middle;
		}
	</style>
	<div class="card-box">
        <table id="tablePricing" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th><input type="checkbox" disabled/></th>
                    <th>Group / Name</th>
                    <th>Role</th>
                    <th>Last signed in</th>
                    <th>Status</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

@endsection