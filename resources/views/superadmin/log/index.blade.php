@extends('layouts.app_backend')
@section('navigations')
	<a href="{{ route('backend.log.list') }}" data-toggle="tooltip" data-placement="bottom" title="reload page" class="btn btn-lg btn-rounded btn-success"><i class="fa fa-refresh"></i></a>
@endsection

@section('content')
    <script type="text/javascript">
		var title = document.getElementById('title');
		title.textContent = "Log Activity";
	</script>
    <style type="text/css">
		#tableLog td {
			vertical-align: middle;
		}

		#tableLog{
		    border-collapse: separate;
		    border:1px solid #F1F1F1;
		    border-radius: 7px;
		    width:100%
		}

		#tableLog th {
		    padding: 15px 0px 15px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:14px;
		    font-weight:600;
		}

		#tableLog td  {
		    padding: 13px 0px 13px 10px;
		    border-bottom:1px solid #F1F1F1;
		    font-size:13.5px;
		    color:black;
		}

		#tableLog tbody tr:last-child td{
		    border-bottom: none;
		}

		#tableLog tbody tr:hover {
		    background-color: #f0f0f0;
		}

        .search-table-outter{ 
            overflow-x: scroll; 
        }
	</style>
    <div class="search-table-outter">
        <form id="app-filter" class="form-inline" action="" method="GET">
            <select name="sort" id="sort" onchange="getFilter()" class="form-control">
                <option value="DESC" {{ !empty(request()->input('sort')) && request()->input('sort') == "DESC" ? "selected":"" }}>DESC</option>
                <option value="ASC" {{ !empty(request()->input('sort')) && request()->input('sort') == "ASC" ? "selected":"" }}>ASC</option>
            </select>
            <input type="month" name="date_created" onchange="getFilter()" value="{{ !empty(request()->input('date_created')) ? request()->input('date_created') : '' }}" class="form-control" />
            <select name="client_id" id="client_id" onchange="getFilter()" class="form-control">
                <option value="">All Client</option>
                @if (count($client) > 0)
                    @foreach ($client as $clients)
                        <option value="{{ $clients->client_id }}" {{ !empty(request()->input('client_id')) && request()->input('client_id') == $clients->client_id ? "selected":"" }} >{{ $clients->client_name }}</option>
                    @endforeach
                @endif
            </select>
            <div class="input-group">
                <input type="text" name="description" class="form-control" value="{{ !empty(request()->input('description')) ? request()->input('description') : '' }}" placeholder="filter description" />
                <span class="input-group-btn">
                    <button type="submit" class="btn waves-effect waves-light btn-inverse">Filter</button>
                </span>
            </div>
            <a href="{{ route('backend.log.list') }}" class="btn btn-default btn-md" data-toggle="tooltip" title="Reset Log">
                <i class="fa fa-remove"></i>
            </a>
        </form><br>
        <table class="table-bordered" id="tableLog">
            <thead>
                <tr style="background-color: #F7F7F7;">
                    <th>
                        No
                    </th>
                    <th>Date</th>
                    <th>URL</th>
                    <th>Description</th>
                    <th>Request</th>
                    <th>Response</th>
                    <th>Method</th>
                    <th>Header</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @if(count($log) > 0)
                    @foreach($log as $key => $logs)
                        <tr>
                            <td style="min-width: 50px;" align="center">{{ $log->firstItem() + $loop->index }}</td>
                            <td style="min-width: 100px;">{{ $logs->created_at }}</td>
                            <td style="min-width: 200px;"><div style="word-break: break-word;">{{ $logs->url }}</div></td>
                            <td style="min-width: 300px;"><div style="word-break: break-word;">{{ $logs->description }}</div></td>
                            <td style="min-width: 300px;"><div style="word-break: break-word;">{{ $logs->request }}</div></td>
                            <td style="min-width: 100px;">{{ $logs->response }}</td>
                            <td style="min-width: 80px;">{{ $logs->method }}</td>
                            <td style="min-width: 300px;"><div style="word-break: break-word;">{!! Str::limit($logs->header, 100) !!} <a href="#modal-detail-header" data-toggle="modal" data-header="{{ json_encode($logs->header) }}" onclick="getDetailHeader(this)">Detail</a></div></td>
                            <td style="min-width: 150px;">{{ $logs->ip }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    {{ $log->withQueryString()->links() }}

    <div id="modal-detail-header" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="titleModal">Detail Header</h4>
                </div>
                <div class="modal-body">
                    <div id="result_header"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function getFilter(){
            $("#app-filter").submit();
        }

        function getDetailHeader(element) {
            $("#result_header").text($(element).data('header'));
        }
    </script>
@endpush