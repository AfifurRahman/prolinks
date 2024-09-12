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

@php
    $index = 0;
@endphp

@section('content')
    <link href="{{ url('clientuser/documentindex.css') }}" rel="stylesheet" type="text/css" />

    <div class="box_helper">
        <h2 id="title" class="project-title">
            Recycle bin
        </h2>
        <div class="button_helper">
        <!--    <a class="btn-helper" onclick="permanentDeleteAll()">Empty recycle bin</a>
            <a class="alt-btn-helper" onclick="restoreItemsAll()">Restore all items</a> -->
        </div>
    </div>

    <div class="path-box">
        <div class="path">
            <div class="path-text">
                Project
                <i class="fa fa-caret-right" style="margin-left:4px;margin-right:4px;font-size:14px;"></i>
                Subproject
                <i class="fa fa-caret-right" style="margin-left:4px;margin-right:4px;font-size:14px;"></i>
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
                        <th>Index</th>
                        <th>Subproject name</th>
                        <th>Deleted at</th>
                        <th>Deleted by</th>
                        <th data-sortable="false" id="navigationdot"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project as $key => $projects)
                        @foreach($projects->RefSubProject as $subs)
                            @if($subs->subproject_status == '0')
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td>{{ $subs->subproject_name }}</td>
                                    <td>{!! date('d M Y H:i', strtotime($subs->updated_at)) !!}</td>
                                    <td>
                                        {!! \globals::get_user_avatar_small(DB::table('users')->where('id', $subs->updated_by)->value('user_id'), DB::table('users')->where('id', $subs->updated_by)->value('avatar_color')) !!}
                                        &nbsp;{{ !is_null(DB::table('users')->where('id', $subs->updated_by)->value('name')) ? DB::table('users')->where('id', $subs->updated_by)->value('name') : 'Unnamed User' }}    
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-top pull-right">
                                                <li>
                                                    <a onclick="restoreSubproject('{{ $subs->subproject_id }}')">
                                                        <img class="dropdown-icon" src="{{ url('template/images/icon_menu/cut.png') }}">
                                                        Restore
                                                    </a>
                                                </li>
                                                <li>
                                                    <a style="color:red;" onclick="permanentDeleteSubproject('{{ $subs->subproject_id }}')">
                                                        <img class="dropdown-icon" src="{{ url('template/images/icon_menu/trash.png') }}">
                                                        Delete permanently
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
            <p style="margin-top:12px;">Showing {{ $index }} items.</p>
        </div>
    </div>

    @push('scripts')
    <script>
         function showNotification(message) {
            document.querySelector('.notificationtext').textContent = message;
            document.querySelector('.notificationlayer').style.display = 'block';
            setTimeout(() => {
                $('.notificationlayer').fadeOut();
            }, 2000);
            setTimeout(function() {
                location.reload();
            }, 1000);
        }

        function restoreSubproject(subproject) {
            var formData = new FormData();
            formData.append('subprojectID', subproject);

            fetch('{{ route("project.recover-subproject") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN' : '{{ csrf_token() }}'
                },
            })
            showNotification("Subproject recovered!");
        }

        function permanentDeleteSubproject(subproject) {
            var formData = new FormData();
            formData.append('subprojectID', subproject);

            fetch('{{ route("project.permanent-delete-subproject") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN' : '{{ csrf_token() }}'
                },
            })
            showNotification("Subproject permanently removed!");
        }
     </script>
    @endpush
@endsection