@extends('layouts.app_client')

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
    <!-- Edit Folder Name Modal -->
    <div id="delete-folder-modal" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div class="upload-modal-title">
                    <h5 class="modal-delete-file-title">Delete folder</h5>
                </div>
                <button class="modal-close" onclick="document.getElementById('delete-folder-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-text">Deleting this folder will also delete all containing files and folders, are you sure you want to continue? You can't undo this action.</p>
                <div class="form-button">
                    <a onclick="document.getElementById('delete-folder-modal').style.display='none'" class="cancel-btn">Cancel</a>
                    <button class="delete-btn" type="submit">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="box_helper">
        <h2 id="title" style="color:black;font-size:28px;">Search result for "{{ $search }}"</h2>
        @php
            $path = explode("/", $origin);
            $link = (count($path) <= 4) ? route('adminuser.documents.list', base64_encode($path[2].'/'.$path[3])) : route('adminuser.documents.openfolder', base64_encode(DB::table('upload_folders')->where('directory', $origin)->value('basename')));
        @endphp
        <div>
           
        </div>
    </div>


    <div class="box_helper">
        <div>
            <a href="{{ $link }}">
                <h4 style="color:#337ab7;">
                    <i class="fa fa-arrow-left"></i>&nbsp;Exit from search
                </h4>
            </a>
            <!--
            <button class="filter_button">
                <image class="filter_icon" src="{{ url('template/images/icon_menu/filter.png') }}"></image>
                Filter
            </button>
            -->
        </div>
        <div class="searchbox">
                <img class="search_icon" src="{{ url('template/images/icon_menu/search.png') }}">
                <input type="text" name="name" class="searchbar" id="searchInput" value="{{ isset($search) ? $search : '' }}" placeholder="Search sub project...">
        </div>
    </div>
    
    <div class="tableContainer">
        <table class="tableDocument">
            <thead>
                <tr>
                    <th id="check"><input type="checkbox" class="checkbox" id="headerCheckBox" disabled/></th>
                    <th id="index">Index</th>
                    <th id="name">Name</th>
                    <th id="created">Created at</th>
                    <th id="uploaded">Uploaded by</th>
                    <th id="type">Type</th>
                    <th id="size">Size</th>
                    <th id="navigationdot">&nbsp;</th>
                </tr>
            </thead>
            
            @foreach ($folders as $directory)
                @if(DB::table('upload_folders')->where('name', basename($directory))->value('status') == 1)
                    <tr>
                        <td>
                            <input type="checkbox" class="checkbox" disabled/>
                        </td>
                        <td>
                            @php
                                $index = '';
                                $originPath = implode('/', array_slice(explode('/', $origin), 0, 4));

                                foreach(array_slice(explode('/', $origin), 4) as $path) {
                                    $originPath .= '/' . $path;

                                    $index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
                                }
                                $index .= DB::table('upload_folders')->where('parent', $origin)->where('name', basename($directory))->value('index');
                            @endphp
                            {{$index}}
                        </td>
                        <td>
                            <a class="fol-fil" href="{{ route('adminuser.documents.openfolder', base64_encode(DB::table('upload_folders')->where('directory', $origin . '/' . basename($directory))->value('basename'))) }}">
                                <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                                @if(is_null(DB::table('upload_folders')->where('parent', $origin)->where('name', basename($directory))->value('displayname')))
                                    {{ basename($directory) }}
                                @else
                                    {{ DB::table('upload_folders')->where('parent', $origin)->where('name', basename($directory))->value('displayname') }}
                                @endif
                            </a>
                        </td>
                        <td data-sort="{{ DB::table('upload_folders')->where('directory', $directory)->value('created_at') }}"> 
                            {{ \Carbon\Carbon::parse(DB::table('upload_folders')->where('directory', $directory)->value('created_at'))->format('d M Y, H:i') }}
                        </td>
                        <td>
                            {!! \globals::get_user_avatar_small(DB::table('users')->where('user_id',DB::table('upload_folders')->where('directory', $directory)->value('uploaded_by'))->value('name'), DB::table('users')->where('user_id',DB::table('upload_folders')->where('directory', $directory)->value('uploaded_by'))->value('avatar_color')) !!}
                            {{ DB::table('users')->where('user_id',DB::table('upload_folders')->where('directory', $directory)->value('uploaded_by'))->value('name') }}
                        </td>
                        <td>Directory</td>
                        <td>{{ App\Helpers\GlobalHelper::formatBytes(DB::table('upload_files')->where('directory', 'like', '%'. $directory .'%')->where('status','1')->sum('size')) }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-top pull-right">
                                    <li>
                                        <a href="{{ route('adminuser.documents.downloadfolder', base64_encode($directory)) }}">
                                            <img class="dropdown-icon" src="{{ url('template/images/icon_menu/download.png') }}">
                                            Download
                                        </a>    
                                    </li>
                                    @if(Auth::user()->type == \globals::set_role_administrator())
                                        <li>
                                            <a onclick="renameFolder('{{ basename($directory) }}', '{{$index}}', '{{ DB::table('upload_folders')->where('parent', $origin)->where('name', basename($directory))->value('displayname') }}')">
                                                <img class="dropdown-icon" src="{{ url('template/images/icon_menu/edit.png') }}">
                                                Rename
                                            </a>
                                        </li>
                                        <li>
                                            <a style="color:red;" onclick="deleteFolder('{{ base64_encode($directory) }}')">
                                                <img class="dropdown-icon" src="{{ url('template/images/icon_menu/trash.png') }}">
                                                Delete
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
            
            @foreach ($files as $file)
                @if(Auth::user()->type == 1 || Auth::user()->type == 2)
                    @if(DB::table('permissions')->where('user_id',Auth::user()->user_id)->where('fileid', basename($file))->value('permission') == '1' || is_null(DB::table('permissions')->where('user_id',Auth::user()->user_id)->where('fileid', basename($file))->value('permission')))
                        @if(DB::table('upload_files')->where('basename', basename($file))->value('status') == 1)
                            <tr>
                                <td>
                                    @php
                                        $index = '';
                                        $originPath = implode('/', array_slice(explode('/', $origin), 0, 4));

                                        foreach(array_slice(explode('/', $origin), 4) as $path) {
                                            $originPath .= '/' . $path;
                                            $index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
                                        }
                                        $index .= DB::table('upload_files')->where('basename', basename($file))->value('index');
                                    @endphp
                                    {{$index}}
                                </td>
                                <td>
                                    <a class="fol-fil" href="{{ route('adminuser.documents.view', base64_encode(basename($file))) }}">
                                        <image class="file-icon" src="{{ url('template/images/icon_menu/' . pathinfo(DB::table('upload_files')->where('basename', basename($file))->value('name'), PATHINFO_EXTENSION) . '.png') }}" />
                                        {{ DB::table('upload_files')->where('basename',basename($file))->value('name') }}
                                    </a>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified($file))->format('d M Y, H:i') }}
                                </td>
                                <td>
                                    {!! \globals::get_user_avatar_small(DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('name'), DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('avatar_color')) !!}
                                    &nbsp;{{ DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('name')  }}
                                </td>
                                <td>{{ DB::table('upload_files')->where('basename',basename($file))->value('mime_type') }}</td>
                                <td>
                                    {{ App\Helpers\GlobalHelper::formatBytes(Storage::size($file)) }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-top pull-right">
                                            <li>
                                                <a href="{{ route('adminuser.documents.downloadfile', base64_encode(basename($file))) }}">
                                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/download.png') }}">
                                                    Download
                                                </a>
                                            </li>
                                            @if(Auth::user()->type == \globals::set_role_administrator())
                                                <li>
                                                    <a onclick="renameFile('{{ basename($file) }}', '{{ url('template/images/icon_menu/' . pathinfo(DB::table('upload_files')->where('basename', basename($file))->value('name'), PATHINFO_EXTENSION) . '.png') }}', '{{$index}}', '{{ str_replace('.' . pathinfo(DB::table('upload_files')->where('basename',basename($file))->value('name'), PATHINFO_EXTENSION), '', DB::table('upload_files')->where('basename',basename($file))->value('name')) }}')">
                                                        <img class="dropdown-icon" src="{{ url('template/images/icon_menu/edit.png') }}">
                                                        Rename
                                                    </a>
                                                </li>
                                                <li>
                                                    <a style="color:red;" onclick="deleteFile('{{ base64_encode(basename($file)) }}')">
                                                        <img class="dropdown-icon" src="{{ url('template/images/icon_menu/trash.png') }}">
                                                        Delete
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endif
                @elseif (Auth::user()->type == 0)
                    @if(DB::table('upload_files')->where('basename', basename($file))->value('status') == 1)
                        <tr>
                            <td>
                                <input type="checkbox" class="checkbox" disabled/>
                            </td>
                            <td>
                                @php
                                    $index = '';
                                    $originPath = implode('/', array_slice(explode('/', $origin), 0, 4));

                                    foreach(array_slice(explode('/', $origin), 4) as $path) {
                                        $originPath .= '/' . $path;
                                        $index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
                                    }
                                    $index .= DB::table('upload_files')->where('basename', basename($file))->value('index');
                                @endphp
                                {{$index}}
                            </td>
                            <td>
                                <a class="fol-fil" href="{{ route('adminuser.documents.view', base64_encode(basename($file))) }}">
                                    <image class="file-icon" src="{{ url('template/images/icon_menu/' . pathinfo(DB::table('upload_files')->where('basename', basename($file))->value('name'), PATHINFO_EXTENSION) . '.png') }}" />
                                    {{ DB::table('upload_files')->where('basename',basename($file))->value('name') }}
                                </a>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified($file))->format('d M Y, H:i') }}
                            </td>
                            <td>
                                {!! \globals::get_user_avatar_small(DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('name'), DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('avatar_color')) !!}
                                &nbsp;{{ DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('name')  }}
                            </td>
                            <td>{{ DB::table('upload_files')->where('basename',basename($file))->value('mime_type') }}</td>
                            <td>
                                {{ App\Helpers\GlobalHelper::formatBytes(Storage::size($file)) }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-top pull-right">
                                        <li>
                                            <a href="{{ route('adminuser.documents.downloadfile', base64_encode(basename($file))) }}">
                                                <img class="dropdown-icon" src="{{ url('template/images/icon_menu/download.png') }}">
                                                Download
                                            </a>
                                        </li>
                                        @if(Auth::user()->type == \globals::set_role_administrator())
                                            <li>
                                                <a onclick="renameFile('{{ basename($file) }}', '{{ url('template/images/icon_menu/' . pathinfo(DB::table('upload_files')->where('basename', basename($file))->value('name'), PATHINFO_EXTENSION) . '.png') }}', '{{$index}}', '{{ str_replace('.' . pathinfo(DB::table('upload_files')->where('basename',basename($file))->value('name'), PATHINFO_EXTENSION), '', DB::table('upload_files')->where('basename',basename($file))->value('name')) }}')">
                                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/edit.png') }}">
                                                    Rename
                                                </a>
                                            </li>
                                            <li>
                                                <a style="color:red;" onclick="deleteFile('{{ base64_encode(basename($file)) }}')">
                                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/trash.png') }}">
                                                    Delete
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endif
            @endforeach
        </table>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                "bSort": false,
                "dom": 'rtip',
                "stripeClasses": false,
            });

            var table = $('.tableDocument').DataTable();
            table.search('{{$search}}').draw();
            $('.tableDocument').css('visibility', 'visible');

        });
     

        function search() {
            var searchTerm = document.getElementById('searchInput').value;
            window.location.href = "{{ route('adminuser.documents.search') }}?name=" + searchTerm + "&origin=" + "{{ base64_encode($origin) }}";
        }

        function showNotification(message) {
            document.getElementById('upload-modal').style.display = 'none';
            document.querySelector('.notificationtext').textContent = message;
            document.querySelector('.notificationlayer').style.display = 'block';
            setTimeout(() => {
                $('.notificationlayer').fadeOut();
            }, 2000);
            setTimeout(function() {
                location.reload();
            }, 2250);
        };

        function rename(folder) {
            document.getElementById('rename-folder-modal').style.display = 'block';
            document.getElementById('old-name').value = folder;
        };
    </script>
    @endpush
@endsection