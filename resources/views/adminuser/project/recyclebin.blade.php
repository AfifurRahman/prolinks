@extends('layouts.app_client')
@php date_default_timezone_set('Asia/Jakarta'); @endphp

@section('notification')
    <div class="notificationlayer">
        <div class="notification">
            <image class="notificationicon" src="{{ url('template/images/icon_menu/checklist.png') }}"></image>
            <p class="notificationtext"></p>
        </div>
    </div>
@endsection



@section('content')
    <link href="{{ url('clientuser/documentindex.css') }}" rel="stylesheet" type="text/css" />

    <div class="box_helper">
        <h2 id="title" class="project-title">
            Recycle bin
        </h2>
        <div class="button_helper">
            <a class="permissions" onclick="permanentDeleteAll()">Empty recycle bin</a>
            <a class="permissions" onclick="restoreItemsAll()">Restore all items</a>
        </div>
    </div>

    <div class="path-box">
        <div class="path">
            <image class="path-icon" src="{{ url('template/images/icon_menu/briefcase.png') }}" />
            <div class="path-text">
                Recycle bin
            </div>
        </div>
    </div>

    <div class="viewcontainer">
        <div class="box_helper">
            <div style="margin-left:5px;display:flex;">
                <a href="{{ route('project.list-project') }}">
                    <h4 style="color:#337ab7;">
                        <i class="fa fa-arrow-left"></i>&nbsp; Back to project list
                    </h4>
                </a>
            </div>
            <div>
            </div>
        </div>
        
        <div class="tableContainer" >
            <table class="tableDocument">
                <thead>
                    <tr class="headerBar">
                        <th>Subproject name</th>
                        <th>Deleted at</th>
                        <th data-sortable="false" id="navigationdot"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project as $key => $projects)
                        @foreach($projects->RefSubProject as $subs)
                            @if($subs->subproject_status == '0')
                                <tr>
                                    <td>{{ $subs->subproject_name }}</td>
                                    <td>{!! date('d M Y H:i', strtotime($subs->updated_at)) !!}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-top pull-right">
                                                <li>
                                                    <a onclick="">
                                                        <img class="dropdown-icon" src="{{ url('template/images/icon_menu/cut.png') }}">
                                                        Restore
                                                    </a>
                                                </li>
                                                <li>
                                                    <a style="color:red;" onclick="">
                                                        <img class="dropdown-icon" src="{{ url('template/images/icon_menu/trash.png') }}">
                                                        Delete permanently
                                                    </a>
                                                </li>
                                                <li>
                                                    <a onclick="">
                                                        <img class="dropdown-icon" src="{{ url('template/images/icon_menu/info.png') }}">
                                                        Properties
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
     </script>
    @endpush
@endsection