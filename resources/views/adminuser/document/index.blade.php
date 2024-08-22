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
    <style>
        .custom-modal-header {
            padding: 5px;
            width: 95%;
            margin: 0 auto;
            margin-top: 13px;
        }
    </style>


    @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator())
        @include('adminuser.document.modal.upload')
        @include('adminuser.document.modal.upload_preview')
        @include('adminuser.document.modal.create_folder')
    @endif


    @if(Auth::user()->type == \globals::set_role_administrator())
        @include('adminuser.document.modal.rename_folder')
        @include('adminuser.document.modal.rename_file')
        @include('adminuser.document.modal.delete_file')
        @include('adminuser.document.modal.delete_folder')
        @if(Auth::user()->email == "jeansusilo99@gmail.com")
            @include('adminuser.document.modal.permission')
        @else
            @include('adminuser.document.modal.old_permission')
        @endif
    @endif

    <div class="box_helper">
        <h2 id="title" class="project-title">
            @if (empty(DB::table('sub_project')->where('subproject_id', explode('/', $origin)[count(explode('/', $origin)) - 1])->value('subproject_name')))
                Content of Folder "{{DB::table('upload_folders')->where('directory', $origin)->value('displayname')}}"
            @else
                Content of Project "{{ DB::table('sub_project')->where('subproject_id', explode('/', $origin)[count(explode('/', $origin)) - 1])->value('subproject_name') }}"
            @endif
        </h2>
        <div class="button_helper">
            @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator())
                <button class="create-folder" onclick="createFolder()">Add folder</button>
            @endif
            
            @if(Auth::user()->type == \globals::set_role_administrator())
                <button data-target="#modal-add-permission" onclick="setPermission()" data-toggle="modal" class="permissions">Permission</button>
                <!-- <button class="permissions" onclick="setPermission()">Permissions</button> -->
            @endif

            @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator())
                <button class="upload" onclick="document.getElementById('upload-modal').style.display='block'">Upload Files</button>
            @endif        
        </div>
    </div>

    <div class="path-box">
        <div class="path">
            <image class="path-icon" src="{{ url('template/images/icon_menu/briefcase.png') }}" />
            <div class="path-text">
                {{ DB::table('sub_project')->where('subproject_id', explode('/', $origin)[3])->value('subproject_name') }}
                @php $url = implode('/',array_slice(explode('/', $origin,),0,4)); @endphp
                @if (count(explode('/', $origin)) > 4)
                    @foreach(array_slice(explode('/', $origin),4) as $path)
                        @php $url .= '/' . $path; @endphp
                        &nbsp;>&nbsp;&nbsp;
                        <a href="{{ route('adminuser.documents.openfolder', base64_encode(DB::table('upload_folders')->where('directory', $url)->value('basename')) ) }}">{{ DB::table('upload_folders')->where('directory', $url)->value('displayname')}}</a>
                        &nbsp;
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="viewcontainer">
        <div class="box_helper">
            <div style="margin-left:5px;display:flex;">
                @if($directorytype == 0)
                    @if (!empty(DB::table('upload_folders')->where('directory', substr($origin,0,strrpos($origin, '/')))->value('basename')))
                        <a class="fol-fil" href="{{ route('adminuser.documents.openfolder', base64_encode(DB::table('upload_folders')->where('directory', substr($origin,0,strrpos($origin, '/')))->value('basename'))) }}">
                            <h4 style="color:#337ab7;">
                                <i class="fa fa-arrow-left"></i>&nbsp; Back to {{ DB::table('upload_folders')->where('name', explode('/', $origin)[count(explode('/', $origin)) - 2])->value('displayname') }}
                            </h4>
                        </a>
                    @else
                        <a class="fol-fil" href="{{ route('adminuser.documents.list', base64_encode(DB::table('upload_folders')->where('directory', $origin)->value('project_id'). '/'. DB::table('upload_folders')->where('directory', $origin)->value('subproject_id'))) }}">
                            <h4 style="color:#337ab7;">
                                <i class="fa fa-arrow-left"></i>&nbsp; Back to Project {{DB::table('sub_project')->where('subproject_id', explode('/', $origin)[count(explode('/', $origin)) - 2])->value('subproject_name')}}
                            </h4>
                        </a>
                    @endif
                @else
                    @if(Auth::user()->type == \globals::set_role_administrator())
                        <a href="{{ route('project.list-project') }}">
                            <h4 style="color:#337ab7;">
                                <i class="fa fa-arrow-left"></i>&nbsp; Back to project list
                            </h4>
                        </a>
                    @endif
                @endif
                
                <!-- Dropdown copy paste -->
                @if (DB::table('action_document')->where('project_id', $projectID)->where('subproject_id', $subprojectID)->where('user_id', Auth::user()->user_id)->value('status') == "1" ) 
                    <div class="dropdown" id="documentAction" style="visibility:visible;">
                        <button class="clipboard dropdown-toggle" data-toggle="dropdown">
                            <img class="dropdown-icon" src="{{ url('template/images/icon_menu/clipboard.png') }}">
                            1 files in clipboard&nbsp;
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-top pull-left">
                            <li>
                                <a onclick="pasteItem()">
                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/paste.png') }}">
                                    Paste here
                                </a>
                            </li>
                            <li>
                                <a onclick="clearClipboard()">
                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/close.png') }}">
                                    Clear clipboard
                                </a>    
                            </li>
                        </ul>
                    </div>
                @else 
                    <div class="dropdown" id="documentAction" style="visibility:collapse;">
                        <button class="clipboard dropdown-toggle" data-toggle="dropdown">
                            <img class="dropdown-icon" src="{{ url('template/images/icon_menu/clipboard.png') }}">
                            1 files in clipboard&nbsp;
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-top pull-left">
                            <li>
                                <a onclick="pasteItem()">
                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/paste.png') }}">
                                    Paste here
                                </a>
                            </li>
                            <li>
                                <a onclick="clearClipboard()">
                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/close.png') }}">
                                    Clear clipboard
                                </a>    
                            </li>
                        </ul>
                    </div>
                @endif
                
                
                 <!--
                <button class="filter_button">
                    <image class="filter_icon" src="{{ url('template/images/icon_menu/filter.png') }}"></image>
                    Filter
                </button>
    -->
            </div>
            <div class="searchbox">
                    <img class="search_icon" src="{{ url('template/images/icon_menu/search.png') }}">
                    <input type="text" name="name" class="searchbar" id="searchInput" placeholder="Search sub project...">
            </div>
        </div>
        
        <div class="tableContainer" >
        @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator())
            <div id="tableDragArea" ondrop="handleDrop(event)">
        @endif
                <table class="tableDocument">
                    <thead>
                        <tr class="checkToolBar" style="visibility:collapse;">
                            <th data-sortable = "false">
                                <input type="checkbox" class="checkbox" id="headerCheckBox1">
                            </th>
                            <th colspan='6'>
                                <span class="selectedCount">0</span>&nbsp;items selected
                                <button class="miniDownload" onclick="downloadFiles()">Download&nbsp;<span class="selectedCount">0</span>&nbsp;items</button>
                                @if(Auth::user()->type == \globals::set_role_administrator())
                                    <button class="miniClear" onclick="deleteSelections()">Delete&nbsp;<span class="selectedCount">0</span>&nbsp;items</button>
                                @endif
                                <button class="miniClear" onclick="uncheckAll()">Clear Item Selection</button>
                            </th>
                        </tr>
                        <tr class="headerBar">
                            <th data-sortable="false" id="check">
                                <input type="checkbox" class="checkbox" id="headerCheckBox">
                            </th>
                            <th id="index">Index</th>
                            <th id="name">Name</th>
                            <th id="created">Created at</th>
                            <th id="uploaded">Uploaded by</th>
                            <th id="type">Type</th>
                            <th id="size">Size</th>
                            <th data-sortable="false" id="navigationdot">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($folders as $directory)
                            @if(DB::table('upload_folders')->where('name', basename($directory))->value('status') == 1)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox" id="folderCheckBox" data-role="folderCheckBox" value="{{ base64_encode(DB::table('upload_folders')->where('parent', $origin)->where('name', basename($directory))->value('basename')) }}">
                                    </td>
                                    <td data-sort="{{DB::table('upload_folders')->where('parent', $origin)->where('name', basename($directory))->value('index')}}">
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
                                        {!! \globals::get_user_avatar_small(DB::table('upload_folders')->where('directory', $directory)->value('uploaded_by'), DB::table('users')->where('user_id',DB::table('upload_folders')->where('directory', $directory)->value('uploaded_by'))->value('avatar_color')) !!}
                                        &nbsp;{{ DB::table('users')->where('user_id',DB::table('upload_folders')->where('directory', $directory)->value('uploaded_by'))->value('name') }}
                                    </td>
                                    <td>Directory</td>
                                    <td data-sort="{{ DB::table('upload_files')->where('directory', 'like', '%'. $directory .'%')->where('status', '1')->sum('size') }}">{{ App\Helpers\GlobalHelper::formatBytes(DB::table('upload_files')->where('directory', 'like', '%'. $directory .'%')->where('status','1')->sum('size')) }}</td>
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
                                     <!--               <li>
                                                        <a onclick="copyItem('{{ DB::table('upload_folders')->where('parent', $origin)->where('name', basename($directory))->value('basename') }}')">
                                                            <img class="dropdown-icon" src="{{ url('template/images/icon_menu/copy.png') }}">
                                                            Copy
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a onclick="">
                                                            <img class="dropdown-icon" src="{{ url('template/images/icon_menu/cut.png') }}">
                                                            Cut
                                                        </a>
                                                    </li> -->
                                                    <li>
                                                        <a style="color:red;" onclick="deleteFolder('{{ base64_encode($directory) }}')">
                                                            <img class="dropdown-icon" src="{{ url('template/images/icon_menu/trash.png') }}">
                                                            Delete
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a onclick="">
                                                            <img class="dropdown-icon" src="{{ url('template/images/icon_menu/info.png') }}">
                                                            Properties
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
                                            <td><input type="checkbox" class="checkbox" id="fileCheckBox" data-role="fileCheckBox" value="{{ base64_encode(basename($file)) }}" /></td>
                                            <td data-sort="{{DB::table('upload_files')->where('basename', basename($file))->value('index')}}">
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
                                            <td data-sort="{{ DB::table('upload_files')->where('basename', basename($file))->value('created_at') }}">
                                                {{ \Carbon\Carbon::parse(DB::table('upload_files')->where('basename', basename($file))->value('created_at'))->format('d M Y, H:i') }}
                                            </td>
                                            <td>
                                                {!! \globals::get_user_avatar_small(DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'), DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('avatar_color')) !!}
                                                {{ DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('name') }}
                                            </td>
                                            <td>{{ DB::table('upload_files')->where('basename',basename($file))->value('mime_type') }}</td>
                                            <td data-sort="{{ DB::table('upload_files')->where('basename',basename($file))->value('size') }}">
                                                {{ App\Helpers\GlobalHelper::formatBytes(DB::table('upload_files')->where('basename',basename($file))->value('size')) }}
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
                                                                <a onclick="copyItem('{{ basename($file) }}')">
                                                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/copy.png') }}">
                                                                    Copy
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="cutItem('{{ basename($file) }}')">
                                                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/cut.png') }}">
                                                                    Cut
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a style="color:red;" onclick="deleteFile('{{ base64_encode(basename($file)) }}')">
                                                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/trash.png') }}">
                                                                    Delete
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="">
                                                                    <img class="dropdown-icon" src="{{ url('template/images/icon_menu/info.png') }}">
                                                                    Properties
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
                                        <td><input type="checkbox" class="checkbox" id="fileCheckBox" data-role="fileCheckBox" value="{{ base64_encode(basename($file)) }}"/></td>
                                        <td data-sort="{{DB::table('upload_files')->where('basename', basename($file))->value('index')}}">
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
                                        <td data-sort="{{ DB::table('upload_files')->where('basename', basename($file))->value('created_at') }}">
                                            {{ \Carbon\Carbon::parse(DB::table('upload_files')->where('basename', basename($file))->value('created_at'))->format('d M Y, H:i') }}
                                        </td>
                                        <td>
                                        {!! \globals::get_user_avatar_small(DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'), DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('avatar_color')) !!}
                                            &nbsp;{{ DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('name') }}
                                        </td>
                                        <td>{{ DB::table('upload_files')->where('basename',basename($file))->value('mime_type') }}</td>
                                        <td data-sort="{{ DB::table('upload_files')->where('basename',basename($file))->value('size') }}">
                                        {{ App\Helpers\GlobalHelper::formatBytes(DB::table('upload_files')->where('basename',basename($file))->value('size')) }}
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
                                                            <a onclick="copyItem('{{ basename($file) }}')">
                                                                <img class="dropdown-icon" src="{{ url('template/images/icon_menu/copy.png') }}">
                                                                Copy
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a onclick="cutItem('{{ basename($file) }}')">
                                                                <img class="dropdown-icon" src="{{ url('template/images/icon_menu/cut.png') }}">
                                                                Cut
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a style="color:red;" onclick="deleteFile('{{ base64_encode(basename($file)) }}')">
                                                                <img class="dropdown-icon" src="{{ url('template/images/icon_menu/trash.png') }}">
                                                                Delete
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a onclick="">
                                                                <img class="dropdown-icon" src="{{ url('template/images/icon_menu/info.png') }}">
                                                                Properties
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
                    </tbody>
                </table>
            @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator())
            </div>
            @endif
            <p>Showing <span id="tableCounter">0</span>.</p>
            <input id="countFile" type="hidden" value="0">
        </div>
    </div>

    @push('scripts')
    <script>
        let a = 0;
        let files = [];
        let permission = [];
        let filesPath = [];
        let filesChecked = [];

        const fileCounts = document.querySelectorAll('[data-role="fileCheckBox"]');
        const folderCounts = document.querySelectorAll('[data-role="folderCheckBox"]');
        $('#tableCounter').text(fileCounts.length + " files and " + folderCounts.length + " folders");

        document.addEventListener('DOMContentLoaded', function() {
            const dragArea = document.getElementById('dragArea');
            const tableDragArea = document.getElementById('tableDragArea');
            const documentCheckBox = document.querySelectorAll('.checkbox');

            $('#all_checkbox').click(function() {
                $('.setPermissionBox').prop('checked', this.checked);
                $('.setPermissionBox').each(function() {
                    handleChangeBox(this); 
                });
            });

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

            setInterval(function() {
                $.ajax({
                    url: '{{ url('/check-session') }}',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (!response.session_valid) {
                            location.reload();
                        }
                    },
                    error: function() {
                        location.reload();
                    }
                });
            }, 1000);

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

            @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator())
            dragArea.addEventListener('dragover', e => {
                e.preventDefault();
                dragArea.classList.add('highlight');
            });

            dragArea.addEventListener('dragleave', () => {
                dragArea.classList.remove('highlight');
            });

            dragArea.addEventListener('drop', e => {
                e.preventDefault();
                dragArea.classList.remove('highlight');
            });

            tableDragArea.addEventListener('dragover', e => {
                e.preventDefault();
                tableDragArea.classList.add('highlight');
            });

            tableDragArea.addEventListener('dragleave', () => {
                tableDragArea.classList.remove('highlight');
            });

            tableDragArea.addEventListener('drop', e => {
                e.preventDefault();
                tableDragArea.classList.remove('highlight');
            });
            @endif

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
                "order" : [[1, "asc"]],
                "stripeClasses": false,
            });

            $('.tableDocument').css('visibility', 'visible');
        });

        function search() {
            var searchTerm = document.getElementById('searchInput').value;
            window.location.href = "{{ route('adminuser.documents.search') }}?name=" + searchTerm + "&origin=" + "{{ base64_encode($origin) }}";
        }

        @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator())
            function handleDrop(event) {
                event.preventDefault();

                var items = event.dataTransfer.items;

                for (var i = 0; i < items.length; i++) {
                    var entry = items[i].webkitGetAsEntry();
                    if (entry) {
                        traverseFileFolder(entry);
                    }
                }

                function traverseFileFolder(entry, path = "") {
                    if (entry.isFile) {
                        entry.file(function(file) {
                            files.push(file);
                            filesPath.push(path);
                            displayFileData(files, filesPath);
                        });
                    } else if (entry.isDirectory) {
                        var dirReader = entry.createReader();
                        dirReader.readEntries(function(entries) {
                            entries.forEach(function(ent) {
                                traverseFileFolder(ent, path + entry.name + "/");
                            });
                        });
                    } 
                }
            }
        @endif

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

        function flashNotification(message) {
            document.querySelector('.notificationtext').textContent = message;
            document.querySelector('.notificationlayer').style.display = 'block';
            setTimeout(() => {
                $('.notificationlayer').fadeOut();
            }, 2000);
        }

        @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator())
            function handleFileSelection(input) {
                for (let i = 0; i < input.files.length; i++) {
                    files.push(input.files[i]);
                    filesPath.push("");
                }
                displayFileData(files, filesPath);
            }
       

            function displayFileData(files, filesPath) {
                document.getElementById('upload-modal').style.display='none';
                document.getElementById('upload-preview-modal').style.display='block';

                const tableBody = document.getElementById('upload-preview-list');
                tableBody.innerHTML = '';

                files.forEach((file, index) => {
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>${file.name}</td>
                        <td>${convertByte(file.size)}</td>
                        <td><button onclick="removeFile(${index})" id="${index}" class="removeFileButton"><i class="fa fa-times"></i></button></td>
                    `;
                    tableBody.appendChild(newRow);
                });

                $('#uploadFileSubmit').on('click', function(e) {
                    if (a == 0) {
                        a = 1;
                        e.preventDefault();
                        $('.removeFileButton').html('<i class="fas fa-circle-notch fa-spin"></i><br><span class="uploadPercentage">0%</span>');
                        $('.removeFileButton').prop("disabled", true);
                        $('#uploadFileSubmit').prop("disabled", true);
                        $('.cancel-btn').prop("disabled", true);
                        $('.modal-close').prop("disabled", true);
                        $("#uploadFileSubmit").removeClass("upload-btn");
                        $('#browseFiles').hide();
                        $('#clearFiles').hide();

                        files.forEach((file, index) => {
                            const formData = new FormData();
                            formData.append("location", "{{ base64_encode($origin) }}");
                            formData.append('file[]', file);
                            formData.append('filePath[]', filesPath[index]);

                            const xhr = new XMLHttpRequest();

                            xhr.upload.addEventListener('progress', (e) => {
                                if (e.lengthComputable) {
                                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                                    document.getElementById(index).querySelector('.uploadPercentage').textContent = percentComplete + "%";
                                    if (percentComplete >= 100) {
                                        document.getElementById(index).querySelector('i').className = 'fas fa-solid fa-check';
                                        if (index + 1 >= files.length) {
                                            setTimeout(function() {
                                                document.getElementById('upload-preview-modal').style.display = 'none';
                                                showNotification("Successfully uploaded the files");
                                            }, 2500);
                                        }
                                    }
                                }
                            });

                            xhr.open('POST', '{{ route("adminuser.documents.upload") }}');
                            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                            xhr.send(formData);
                        });
                        
                      
                    }
                });
            }

            function removeFile(index) {
                files.splice(index, 1); 
                filesPath.splice(index, 1); 
                displayFileData(files, filesPath);
            }

            function clearFiles() {
                files = [];
                displayFileData(files);
            }

            function convertByte(size) {
                if (size >= 1073741824) {
                    return (size / 1073741824).toFixed(2) + ' GB';
                } else if (size >= 1048576) {
                    return (size / 1048576).toFixed(2) + ' MB';
                } else if (size >= 1024) {
                    return (size / 1024).toFixed(2) + ' KB';
                } else if (size >= 0) {
                    return size + ' bytes';
                }
            }

            function setPermission() {
                document.getElementById('set-permission-modal').style.display='block';
                document.getElementById('permission-file-list-table').style.display="none";
                $('#setPermissionButton').prop("disabled", true);

                var formData = new FormData();
                formData.append('projectid', '{{$projectID}}');
                formData.append('subprojectid', '{{$subprojectID}}')

                fetch('{{ route("adminuser.documents.checkpermission") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    permission = data.permissionlist;
                });
            }

            function savePermission() {
                fetch('{{ route("adminuser.documents.setpermission") }}', {
                    method: 'POST',
                    body: JSON.stringify({ permission: permission }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'  
                    },
                })
                .then(response => response.json())
                .then(data => {
                    // $("#modal-add-permission").modal('hide');
                    $("#set-permission-modal").modal('hide');
                    showNotification(data.message);
                })
            }
            
            function handleChangeBox(checkbox) {
                if (permission[$("#IDuser").val()]) {
                    for (const obj of permission[$("#IDuser").val()]) {
                        if (obj.id === checkbox.value) {
                            obj.permission = checkbox.checked ? '1' : '0';
                            break; 
                        }
                    }
                }
                checkAllListener()
            }

            function checkAllListener() {
                const FileCount = $('.setPermissionBox').length - 1;
                var FileChecked = $('.setPermissionBox:checked').length;
            
                if (FileChecked > FileCount) {
                    $('#all_checkbox').prop('checked', true); 
                } else if (FileChecked == FileCount && !($('#all_checkbox').prop('checked'))) {
                    $('#all_checkbox').prop('checked', true);
                } else { 
                    $('#all_checkbox').prop('checked', false);
                }
            }

            function checkUserPermission(user) {
                $("#IDuser").attr("value",user);
                document.getElementById('permission-file-list-table').style.display="block";
                $('#setPermissionButton').prop("disabled", false);

                var checkboxIds = [];
                $("#fileList input[type='checkbox']").each(function() {
                    var checkboxId = $(this).attr("id"); 
                    checkboxIds.push(checkboxId);
                });
                checkboxIds.forEach(function(checkboxId) {
                    $("#" + checkboxId).prop("checked", false);
                });

                permission[user].forEach(obj => {
                        if (`${obj.permission}`== 1){
                        $("#" + `${obj.id}`).prop("checked", true);
                    }   
                });

                checkAllListener()
            }

            function createFolder() {
                document.getElementById('create-folder-modal').style.display='block';

                $('#createFolderSubmit').on('click', function(e) {
                    e.preventDefault();
                    var formData = new FormData();

                    formData.append('folderName', $('#folderName').val());
                    formData.append('location', '{{ base64_encode($origin) }}')

                    fetch('{{ route("adminuser.documents.createfolder") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('create-folder-modal').style.display='none';
                        showNotification(data.message);
                    });
                });
            }
        @endif

        @if(Auth::user()->type == \globals::set_role_administrator())
            function deleteFile(file) {
                document.getElementById('delete-file-modal').style.display='block';

                $('#deleteFileSubmit').on('click', function(e) {
                    e.preventDefault();
                    var formData = new FormData();

                    formData.append('item', file);
                    
                    fetch('{{ route("adminuser.documents.delete") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('delete-file-modal').style.display='none';
                        showNotification(data.message);
                    });
                });
            }
        @endif

        function uncheckAll() {
            $('.checkbox').prop('checked', false);
            $(".headerBar").css("visibility", "visible");
            $(".checkToolBar").css("visibility", "collapse");
        }

        function downloadFiles() {
            var formData = new FormData();
            
            formData.append('files', filesChecked);

            fetch('{{ route("adminuser.documents.downloadfiles")}}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.replace("{{ route('adminuser.documents.downloadzip', '') }}" + '/' + data.link);
                }
            });
        }

        @if(Auth::user()->type == \globals::set_role_administrator())
            function deleteSelections() {
                filesChecked.forEach(deleteSelection);

                function deleteSelection(item) {
                    var formData = new FormData();

                    formData.append('item', item);

                    fetch('{{ route("adminuser.documents.delete") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('delete-file-modal').style.display='none';
                        showNotification(data.message);
                    });
                }
            }

            function renameFolder(folder, index, name) {
                document.getElementById('rename-folder-modal').style.display='block';
                $("#folder-index").attr("value", index);
                $("#newFolderName").attr("value", name);

                $('#renameFolderSubmit').on('click', function(e) {
                    e.preventDefault();
                    var formData = new FormData();

                    formData.append('name', folder);
                    formData.append('newname', $('#newFolderName').val());
                    formData.append('location', '{{ base64_encode($origin) }}')

                    fetch('{{ route("adminuser.documents.renamefolder") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('rename-folder-modal').style.display='none';
                        showNotification(data.message);
                    });
                });
            }

            function deleteFolder(folder) {
                document.getElementById('delete-folder-modal').style.display='block';

                $('#deleteFolderSubmit').on('click', function(e) {
                    e.preventDefault();
                    var formData = new FormData();
                    
                    formData.append('item', folder);

                    fetch('{{ route("adminuser.documents.delete") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('delete-folder-modal').style.display='none';
                        showNotification(data.message);
                    });
                });
            }

            function renameFile(file, icon, index, name) {
                document.getElementById('rename-file-modal').style.display = 'block';
                $(".rename-file-icon").attr("src", icon);
                $("#file-index").attr("value", index);
                $("#new-file-name").attr("value", name);


                $('#renameFileSubmit').on('click', function(e) {
                    e.preventDefault();
                    var formData = new FormData();

                    formData.append('old_name', file);
                    formData.append('new_name', $('#new-file-name').val());

                    fetch('{{ route("adminuser.documents.renamefile") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('rename-file-modal').style.display='none';
                        showNotification(data.message);
                    });
                });
            }

            function copyItem($item) {
                var formData = new FormData();
                formData.append('items', $item);

                fetch('{{ route("adminuser.documents.copy") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        $("#documentAction").css("visibility", "visible");
                        flashNotification('Copied to clipboard');
                    }
                });
            }

            function cutItem($item) {
                var formData = new FormData();
                formData.append('items', $item);

                fetch('{{ route("adminuser.documents.cut") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        $("#documentAction").css("visibility", "visible");
                        flashNotification('Cut to clipboard');
                    }
                });
            }

            function pasteItem() {
                var formData = new FormData();
                formData.append('location', '{{ $origin }}');
                
                fetch('{{ route("adminuser.documents.paste") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        showNotification('Pasted!');
                    } 
                });
            }

            function clearClipboard() {
                var formData = new FormData();
                
                fetch('{{ route("adminuser.documents.clear") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        $("#documentAction").css("visibility", "collapse");
                        flashNotification('Clipboard cleared!');
                    } 
                });
            }

            $("#permission-file-list-table").dataTable({
                "bPaginate": false,
                "bInfo": false,
                "bSort": false,
                "bAutoWidth": false,
            });
        @endif
    </script>
    @endpush
@endsection