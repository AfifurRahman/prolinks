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
    @include('adminuser.document.modal.permanent_delete')
    @include('adminuser.document.modal.restore')
    <link href="{{ url('clientuser/documentindex.css') }}" rel="stylesheet" type="text/css" />

    <div class="box_helper">
        <h2 id="title" class="project-title">
            Recycle bin
        </h2>
        <div class="button_helper">
            <a class="permissions" href="">Empty recycle bin</a>
            <a class="permissions" href="">Restore all items</a>
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
                <a href="##" onClick="history.go(-2); return false;">
                    <h4 style="color:#337ab7;">
                        <i class="fa fa-arrow-left"></i>&nbsp; Back to subproject
                    </h4>
                </a>
            </div>
            <div class="searchbox">
                    <img class="search_icon" src="{{ url('template/images/icon_menu/search.png') }}">
                    <input type="text" name="name" class="searchbar" id="searchInput" placeholder="Search recycle bin...">
            </div>
        </div>
        
        <div class="tableContainer" >
            <table class="tableDocument">
                <thead>
                    <tr class="checkToolBar" style="visibility:collapse;">
                        <th data-sortable = "false">
                            <input type="checkbox" class="checkbox" id="headerCheckBox1">
                        </th>
                        <th colspan='6'>
                            <span class="selectedCount">0</span>&nbsp;items selected
                            <button class="miniDownload" onclick="downloadFiles()">Permanently remove&nbsp;<span class="selectedCount">0</span>&nbsp;items</button>
                            <button class="miniDownload" onclick="downloadFiles()">Restore&nbsp;<span class="selectedCount">0</span>&nbsp;items</button>
                            <button class="miniClear" onclick="uncheckAll()">Clear Item Selection</button>
                        </th>
                    </tr>
                    <tr class="headerBar">
                        <th data-sortable="false" id="check">
                            <input type="checkbox" class="checkbox" id="headerCheckBox">
                        </th>
                        <th id="index">Index</th>
                        <th id="name">Name</th>
                        <th id="created">Deleted at</th>
                        <th id="uploaded">Deleted by</th>
                        <th id="type">Type</th>
                        <th id="size">Size</th>
                        <th data-sortable="false" id="navigationdot">&nbsp;</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($folders as $folder)
                        <tr>
                            <td>
                                <input type="checkbox" class="checkbox" id="folderCheckBox" data-role="folderCheckBox" value="{{ $folder->basename }}">
                            </td>
                            <td>
                                @php
                                    $index = '';
                                    $originPath = implode('/', array_slice(explode('/', $folder->directory), 0, 4));

                                    foreach(array_slice(explode('/', $folder->directory), 4) as $path) {
                                        $originPath .= '/' . $path;

                                        $index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
                                    }
                                    $index = rtrim($index, '.');
                                @endphp
                                {{$index}}
                            </td>
                            <td>
                                <a class="fol-fil">
                                    <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                                    @if(is_null($folder->display_name))
                                        {{ $folder->name }}
                                    @else
                                        {{ $folder->displayname }}
                                    @endif
                                </a>
                            </td>
                            <td data-sort="{{ DB::table('upload_folders')->where('directory', $folder)->value('created_at') }}"> 
                                {{ \Carbon\Carbon::parse(DB::table('upload_folders')->where('directory', $folder)->value('created_at'))->format('d M Y, H:i') }}
                            </td>
                            <td> 
                                {!! \globals::get_user_avatar_small($folder->uploaded_by), DB::table('users')->where('user_id', $folder->uploaded_by)->value('avatar_color') !!}
                                &nbsp;{{ DB::table('users')->where('user_id', $folder->uploaded_by)->value('name') }}
                            </td>
                            <td>Directory</td>
                            <td data-sort="{{ DB::table('upload_files')->where('directory', 'like', '%'. $folder .'%')->where('status', '1')->sum('size') }}">{{ App\Helpers\GlobalHelper::formatBytes(DB::table('upload_files')->where('directory', 'like', '%'. $folder .'%')->where('status','1')->sum('size')) }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-top pull-right">
                                        <li>
                                            <a style="color:red;" onclick="restoreItem('{{ base64_encode($folder->basename) }}')">
                                                <img class="dropdown-icon" src="{{ url('template/images/icon_menu/trash.png') }}">
                                                Restore
                                            </a>
                                        </li>
                                        <li>
                                            <a style="color:red;" onclick="permanentDelete('{{ base64_encode($folder->basename) }}')">
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
                    @endforeach
                    
                    @foreach($files as $file)
                        <tr>
                            <td><input type="checkbox" class="checkbox" id="fileCheckBox" data-role="fileCheckBox" value="{{ $file->basename }}" /></td>
                            <td>
                                @php
                                    $index = '';
                                    $originPath = implode('/', array_slice(explode('/', $file->directory), 0, 4));

                                    foreach(array_slice(explode('/', $file->directory), 4) as $path) {
                                        $originPath .= '/' . $path;
                                        $index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
                                    }
                                    $index .= $file->index;
                                @endphp
                                {{$index}}
                            </td>
                            <td>
                                <a class="fol-fil">
                                    <image class="file-icon" src="{{ url('template/images/icon_menu/' . pathinfo($file->name, PATHINFO_EXTENSION) . '.png') }}" />
                                    {{ $file->name }}
                                </a>
                            </td>
                            <td data-sort="{{ DB::table('upload_files')->where('basename', basename($file))->value('created_at') }}">
                                {{ \Carbon\Carbon::parse($file->updated_at)->format('d M Y, H:i') }}
                            </td>
                            <td>
                                {!! \globals::get_user_avatar_small($file->uploaded_by, DB::table('users')->where('user_id', $file->uploaded_by)->value('avatar_color')) !!}
                                {{ DB::table('users')->where('user_id', $file->uploaded_by)->value('name') }}
                            </td>
                            <td>{{ $file->mime_type }}</td>
                            <td data-sort="{{ DB::table('upload_files')->where('basename',basename($file))->value('size') }}">
                                {{ App\Helpers\GlobalHelper::formatBytes($file->size) }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-top pull-right">
                                        <li>
                                            <a onclick="restoreItem('{{ base64_encode($file->basename) }}')">
                                                <img class="dropdown-icon" src="{{ url('template/images/icon_menu/cut.png') }}">
                                                Restore
                                            </a>
                                        </li>
                                        <li>
                                            <a style="color:red;" onclick="permanentDelete('{{ base64_encode($file->basename) }}')">
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
                    @endforeach
                </tbody>
            </table>
            <p>Showing <span id="tableCounter">0</span>.</p>
            <input id="countFile" type="hidden" value="0">
        </div>
    </div>

    @push('scripts')
    <script>
        function permanentDelete(itemID) {
            document.getElementById('permanent-delete-modal').style.display='block';

            $('#deleteFileSubmit').on('click', function(e) {
                    e.preventDefault();

                var formData = new FormData();
                formData.append('item', itemID);

                fetch('{{ route("adminuser.documents.permanentdelete") }}', {
                    method : 'POST',
                    body : formData,
                    headers : {
                        'X-CSRF-TOKEN' : '{{ csrf_token() }}'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('delete-file-modal').style.display='none';
                    showNotification(data.message);
                });
            });
        }

        function restoreItem(itemID) {
            var formData = new FormData();
            formData.append('item', itemID);

            fetch('{{ route("adminuser.documents.restore") }}', {
                method : 'POST',
                body : formData,
                headers : {
                    'X-CSRF-TOKEN' : '{{ csrf_token() }}'
                },
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('delete-file-modal').style.display='none';
                showNotification(data.message);
            });
        }

        let filesChecked = [];

        const fileCounts = document.querySelectorAll('[data-role="fileCheckBox"]');
        const folderCounts = document.querySelectorAll('[data-role="folderCheckBox"]');
        $('#tableCounter').text(fileCounts.length + " files and " + folderCounts.length + " folders");

        document.addEventListener('DOMContentLoaded', function() {
            const dragArea = document.getElementById('dragArea');
            const tableDragArea = document.getElementById('tableDragArea');
            const documentCheckBox = document.querySelectorAll('.checkbox');

            $('#headerCheckBox').change(function() {
                $('#headerCheckBox1').prop('checked', this.checked);
                $('input[data-role="folderCheckBox"]').prop('checked', this.checked);
                $('input[data-role="fileCheckBox"]').prop('checked', this.checked);
            });

            $('#headerCheckBox1').change(function() {
                $('#headerCheckBox').prop('checked', this.checked);
                $('input[data-role="folderCheckBox"]').prop('checked', this.checked);
                $('input[data-role="fileCheckBox"]').prop('checked', this.checked);
            });


            documentCheckBox.forEach(function (CheckBox) {
                CheckBox.addEventListener('change', function() {
                    var checked = $('#folderCheckBox:checked').length + $('#fileCheckBox:checked').length;
                    var checkedValues = [];
                    
                    $('#fileCheckBox:checked').each(function() {
                        checkedValues.push($(this).val()); 
                    });

                    $('#folderCheckBox:checked').each(function() {
                        checkedValues.push($(this).val()); 
                    });

                    filesChecked = checkedValues;
                    if(checked > 0) {
                        $(".headerBar").css("visibility", "collapse");
                        $(".checkToolBar").css("visibility", "visible");
                        $('.selectedCount').text(checked);
                    } else {
                        $(".headerBar").css("visibility", "visible");
                        $(".checkToolBar").css("visibility", "collapse");
                        $('#headerCheckBox').prop('checked', false);
                        $('#headerCheckBox1').prop('checked', false);
                    }
                });
            });

            document.getElementById('searchInput').addEventListener('keypress', function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    var searchTerm = document.getElementById('searchInput').value.trim();
                    if (searchTerm.length >= 3) {
                        search();
                    }
                }
            });

            $('.tableDocument').dataTable({
                "bPaginate": false,
                "bInfo": false,
                "bSort": true,
                "dom": 'rtip',
                "stripeClasses": false,
            });

            $('.tableDocument').css('visibility', 'visible');
        });

        function search() {
            var searchTerm = document.getElementById('searchInput').value;
            window.location.href = "{{ route('adminuser.documents.search') }}?name=" + searchTerm + "&origin=" + "{{ base64_encode($origin) }}";
        }

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

        function uncheckAll() {
            $('.checkbox').prop('checked', false);
            $(".headerBar").css("visibility", "visible");
            $(".checkToolBar").css("visibility", "collapse");
        }
    </script>
    @endpush
@endsection