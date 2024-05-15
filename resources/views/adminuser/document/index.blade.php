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
    <!--Upload Modal-->
    <div id="upload-modal" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div class="upload-modal-title">
                    <h5 class="modal-title-text">Upload files</h5>
                </div>
                <button class="modal-close" onclick="document.getElementById('upload-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            <div class="modal-upload-box">
                <div class="drag-area" id="dragArea" ondrop="handleDrop(event)">
                    <image class="modal-upload-img" style="width:56px;height:56px;" src="{{ url('template/images/icon_menu/modal_upload.png') }}"></image>
                    <span class="header">Drop your file(s) here</span>
                    <button class="modal-upload-btn" onclick="document.getElementById('fileInput').click()">Browse</button>
                    <input id="fileInput" type="file" style="visibility:hidden;position:absolute;" accept=".doc, .pdf, .txt, .docx, .xls, .xlsx, .ppt, .csv, .pptx, image/*, video/*, .zip, .rar, .7z" multiple oninput="handleFileSelection(this)">
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Preview Modal-->
    <div id="upload-preview-modal" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div class="upload-modal-title">
                    <h5 class="modal-title-text">Upload files</h5>
                </div>
                <button class="modal-close" onclick="document.getElementById('upload-preview-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            <div class="modal-body">
                <div class="upload-helper">
                    <button id="browseFiles" class="create-btn" onclick="document.getElementById('fileInput').click()">Browse files</button>
                    <button id="clearFiles"  class="delete-btn" onclick="clearFiles()"><i class="fa fa-times"></i>&nbsp;Clear all</button>
                </div>
                <div class="tableUploadPreview">
                    <table id="upload-preview-table" class="table">
                        <thead>
                            <tr>
                                <th style="width:200px;">File name</th>
                                <th style="width:20%;">Size</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="upload-preview-list">
                            <tr>
                                <td>Decoy.png</td>
                                <td>100 KB</td>
                                <td>Remove</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-button">
                    <button class="cancel-btn" onclick="document.getElementById('upload-preview-modal').style.display='none'">Cancel</button>
                    <button class="upload-btn" id="uploadFileSubmit">Upload files</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rename File Modal -->
    <div id="rename-file-modal" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div class="upload-modal-title">
                    <h5 class="modal-title-text">Rename file</h5>
                </div>
                <button class="modal-close" onclick="document.getElementById('rename-file-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>

            <div class="modal-body">
                <div class="rename-modal">
                    <div class="rename-modal1">
                        <label class="modal-form-input">Index</label>
                        <input type="text" id="file-index" class="form-control" disabled/>
                    </div>
                    <div class="rename-modal2">
                        <label class="modal-form-input">File name</label><label style="color:red;">*</label>
                        <div class="rename-file-input">
                            <image class="rename-file-icon" />
                            <input type="text" class="form-control" id="new-file-name" placeholder="Enter file name without extension"/>
                        </div>
                    </div>
                </div>
                <div class="form-button">
                    <a class="cancel-btn" onclick="document.getElementById('rename-file-modal').style.display='none'">Cancel</a>
                    <button class="create-btn" id="renameFileSubmit">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Folder Modal -->
    <div id="create-folder-modal" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div class="upload-modal-title">
                    <h5 class="modal-title-text">Create folder</h5>
                </div>
                <button class="modal-close" onclick="document.getElementById('create-folder-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>

            <div class="modal-body">
                <label>Folder name</label>
                <input type="text" class="form-control" id="folderName"></input>
                <div class="form-button">
                    <a onclick="document.getElementById('create-folder-modal').style.display='none'" class="cancel-btn">Cancel</a>
                    <button class="create-btn" id="createFolderSubmit">Create Folder</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete File Modal -->
    <div id="delete-file-modal" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div class="upload-modal-title">
                    <h5 class="modal-delete-file-title">Delete file</h5>
                </div>
                <button class="modal-close" onclick="document.getElementById('delete-file-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-text">Deleting this file will permanently remove it, are you sure you want to continue? You can't undo this action.</p>
                <div class="form-button">
                    <a onclick="document.getElementById('delete-file-modal').style.display='none'" class="cancel-btn">Cancel</a>
                    <button class="delete-btn" id="deleteFileSubmit">Delete</button>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Delete Folder Modal -->
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
                    <button class="delete-btn" id="deleteFolderSubmit">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rename Folder Modal -->
    <div id="rename-folder-modal" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div class="upload-modal-title">
                    <h5 class="modal-title-text">Rename folder</h5>
                </div>
                <button class="modal-close" onclick="document.getElementById('rename-folder-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>

            <div class="modal-body">
                <div class="rename-modal">
                    <div class="rename-modal1">
                        <label class="modal-form-input">Index</label>
                        <input type="text" class="form-control" id="folder-index" disabled/>
                    </div>
                    <div class="rename-modal2">
                        <label class="modal-form-input">Folder name</label><label style="color:red;">*</label>
                        <input type="text" class="form-control" id="newFolderName" placeholder="Enter folder name"/>
                    </div>
                    <input type="hidden" id="old-name" name="old_name" value="" />
                </div>
                <div class="form-button">
                    <a class="cancel-btn" onclick="document.getElementById('rename-folder-modal').style.display='none'">Cancel</a>
                    <button class="create-btn" id="renameFolderSubmit">Save changes</button>
                </div>
            </div>
        </div>
    </div>

     <!-- Permission -->
     <div id="permission-modal" class="modal">
        <div class="modal-content-large">
            <div class="modal-topbar">
                <div class="upload-modal-title">
                    <h5 class="modal-title-text">Permission Settings</h5>
                </div>
                <button class="modal-close" onclick="document.getElementById('permission-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>

            <div class="modal-permission-body">
                <div class="permission-user-list">
                    <h4>Users</h4>
                    <table id="permission-user-list-table" >
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($listusers) > 0) 
                                @foreach($listusers->unique('group_id') as $group)
                                    @if(!empty(DB::table('access_group')->where('group_id', $group->group_id)->value('group_name')))
                                        <tr>
                                            <td>
                                                <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/group.png') }}" />
                                            </td>
                                            <td class="permission-user-list-td">
                                                {{ DB::table('access_group')->where('group_id', $group->group_id)->value('group_name') }}
                                            </td>
                                        </tr>
                                        @foreach($listusers as $user)
                                            @if(!is_null(DB::table('assign_project')->where('project_id', explode('/', $origin)[2])->where('user_id', $user->user_id)->value('email')))
                                                @if($user->group_id == $group->group_id)
                                                    <tr>
                                                        <td></td>
                                                        <td>
                                                            <div style="cursor:pointer;">
                                                                <a onclick="checkUserPermission('{{ $user->user_id }}')">
                                                                    <p class="permission-user-list-td">{{ $user->name }}</p>
                                                                    <p class="permission-user-list-td2">
                                                                    {{ $user->role == 0 ? 'Administrator' : ($user->role == 1 ? 'Collaborator' : 'Client') }}
                                                                    </p>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                                @foreach($listusers->unique('group_id') as $group)
                                    @if(empty(DB::table('access_group')->where('group_id', $group->group_id)->value('group_name')))
                                        @foreach($listusers as $user)
                                            @if(!is_null(DB::table('assign_project')->where('project_id', explode('/', $origin)[2])->where('user_id', $user->user_id)->value('email')))
                                                @if(empty($user->group_id))
                                                    <tr>
                                                        <td>
                                                            <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/user.png') }}" />
                                                            <br>
                                                            &nbsp;
                                                        </td>
                                                        <td style="cursor:pointer;">
                                                            <div style="cursor:pointer;">
                                                                <a onclick="checkUserPermission('{{ $user->user_id }}')">
                                                                    <p class="permission-user-list-td">{{ $user->name }}</p>
                                                                    <p class="permission-user-list-td2">
                                                                    {{ $user->role == 0 ? 'Administrator' : ($user->role == 1 ? 'Collaborator' : 'Client') }}
                                                                    </p>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div> 
                <div class="permission-file-list">
                    <h4 id="permissionUser">Select a user</h4>
                    <table id="permission-file-list-table" class="table table-sm">
                        <thead>
                            <tr>
                                <th>
                                    File Name
                                </th>
                                <th>

                                </th>
                            </tr>
                        </thead>
                        <tbody id="fileList">
                            @foreach($fileList as $file)
                                @if( DB::table('upload_files')->where('basename', $file)->value('status') == 1 )
                                    <tr>
                                        <td>
                                            {{DB::table('upload_files')->where('basename', $file)->value('name')}}
                                        </td>
                                        <td>
                                            <input type="checkbox" id="{{$file}}" class="setPermissionBox" value="{{$file}}"></input>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div> 
            </div>

            <input type="hidden" id="IDuser" value="">

            <div class="modal-footer">
                <a class="cancel-btn" onclick="document.getElementById('permission-modal').style.display='none'">Cancel</a>
                <button class="create-btn" id="setPermissionButton" onclick="savePermission()">Save settings</button>
            </div>  
        </div>
    </div>


    <div class="box_helper">
        <h2 id="title" style="color:black;font-size:28px;">
            @if (empty(DB::table('sub_project')->where('subproject_id', explode('/', $origin)[count(explode('/', $origin)) - 1])->value('subproject_name')))
                {{DB::table('upload_folders')->where('directory', $origin)->value('displayname')}}
            @else
                {{ DB::table('sub_project')->where('subproject_id', explode('/', $origin)[count(explode('/', $origin)) - 1])->value('subproject_name') }}
            @endif
        </h2>
        <div class="button_helper">
            @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator())
                <button class="create-folder" onclick="createFolder()">Add folder</button>
            @endif
            
            @if(Auth::user()->type == \globals::set_role_administrator())
                <button class="permissions" onclick="setPermission()">Permissions</button>
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

    <p>
        
    </p>

    <div class="viewcontainer">
        <div class="box_helper">
            <div style="margin-left:5px">
                @if($directorytype == 0)
                    @if (!empty(DB::table('upload_folders')->where('directory', substr($origin,0,strrpos($origin, '/')))->value('basename')))
                        <a class="fol-fil" href="{{ route('adminuser.documents.openfolder', base64_encode(DB::table('upload_folders')->where('directory', substr($origin,0,strrpos($origin, '/')))->value('basename'))) }}">
                            <h4 style="color:#337ab7;">
                                <i class="fa fa-arrow-left"></i> Back
                            </h4>
                        </a>
                    @else
                        <a class="fol-fil" href="{{ route('adminuser.documents.list', base64_encode(DB::table('upload_folders')->where('directory', $origin)->value('project_id'). '/'. DB::table('upload_folders')->where('directory', $origin)->value('subproject_id'))) }}">
                            <h4 style="color:#337ab7;">
                                <i class="fa fa-arrow-left"></i> Back
                            </h4>
                        </a>
                    @endif
                @else
                    @if(Auth::user()->type == \globals::set_role_administrator())
                        <a href="{{ route('project.list-project') }}">
                            <h4 style="color:#337ab7;">
                                <i class="fa fa-arrow-left"></i> Back
                            </h4>
                        </a>
                    @endif
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
        
        <div class="tableContainer">
            <table class="tableDocument">
                <thead>
                    <tr>
                        <th data-sortable = "false" id="check"><input type="checkbox" class="checkbox" disabled/></th>
                        <th id="index">Index</th>
                        <th id="name">File name</th>
                        <th id="created">Created at</th>
                        <th id="uploaded">Uploaded by</th>
                        <th data-sortable = "false" id="size">Size / type</th>
                        <th data-sortable = "false" id="navigationdot">&nbsp;</th>
                    </tr>
                </thead>
                @if($directorytype == 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            @if (!empty(DB::table('upload_folders')->where('directory', substr($origin,0,strrpos($origin, '/')))->value('basename')))
                                <a class="fol-fil" href="{{ route('adminuser.documents.openfolder', base64_encode(DB::table('upload_folders')->where('directory', substr($origin,0,strrpos($origin, '/')))->value('basename'))) }}">
                                    <image class="up-arrow" src="{{ url('template/images/icon_menu/arrow.png') }}" />
                                    Up to  {{ DB::table('upload_folders')->where('name', explode('/', $origin)[count(explode('/', $origin)) - 2])->value('displayname') }}
                                </a>
                            @else
                                <a class="fol-fil" href="{{ route('adminuser.documents.list', base64_encode(DB::table('upload_folders')->where('directory', $origin)->value('project_id'). '/'. DB::table('upload_folders')->where('directory', $origin)->value('subproject_id'))) }}">
                                    <image class="up-arrow" src="{{ url('template/images/icon_menu/arrow.png') }}" />
                                    Up to {{DB::table('sub_project')->where('subproject_id', explode('/', $origin)[count(explode('/', $origin)) - 2])->value('subproject_name')}}
                                </a>
                            @endif
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif
                
                @foreach ($folders as $directory)
                    @if(DB::table('upload_folders')->where('name', basename($directory))->value('status') == 1)
                        <tr>
                            <td><input type="checkbox" class="checkbox" /></td>
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
                            <td>{{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified($directory))->format('d M Y, H:i') }}</td>
                            <td> {{ DB::table('users')->where('user_id',DB::table('upload_folders')->where('directory', $directory)->value('uploaded_by'))->value('name') }}</td>
                            <td>Directory</td>
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
                                    <td><input type="checkbox" class="checkbox" /></td>
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
                                        <a class="fol-fil" href="{{ route('adminuser.documents.downloadfile', [ base64_encode($origin), base64_encode(basename($file)) ] ) }}">
                                            <image class="file-icon" src="{{ url('template/images/icon_menu/' . pathinfo(DB::table('upload_files')->where('basename', basename($file))->value('name'), PATHINFO_EXTENSION) . '.png') }}" />
                                            {{ DB::table('upload_files')->where('basename',basename($file))->value('name') }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified($file))->format('d M Y, H:i') }}
                                    </td>
                                    <td>{{ DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('name')  }}</td>
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
                                                    <a href="{{ route('adminuser.documents.downloadfile', [ base64_encode($origin), base64_encode(basename($file)) ] ) }}">
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
                                <td><input type="checkbox" class="checkbox" /></td>
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
                                    <a class="fol-fil" href="{{ route('adminuser.documents.downloadfile', [ base64_encode($origin), base64_encode(basename($file)) ] ) }}">
                                        <image class="file-icon" src="{{ url('template/images/icon_menu/' . pathinfo(DB::table('upload_files')->where('basename', basename($file))->value('name'), PATHINFO_EXTENSION) . '.png') }}" />
                                        {{ DB::table('upload_files')->where('basename',basename($file))->value('name') }}
                                    </a>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified($file))->format('d M Y, H:i') }}
                                </td>
                                <td>{{ DB::table('users')->where('user_id', DB::table('upload_files')->where('basename', basename($file))->value('uploaded_by'))->value('name')  }}</td>
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
                                                <a href="{{ route('adminuser.documents.downloadfile', [ base64_encode($origin), base64_encode(basename($file)) ] ) }}">
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
    </div>
    @push('scripts')
    <script>
        let files = [];
        let a = 0;
        
        document.addEventListener('DOMContentLoaded', function() {
            const dragArea = document.getElementById('dragArea');

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

        // Handle drag and drop file
        function handleDrop(event) {
            async function traverseFileTreePromise(item, path = "", folder) {
                return new Promise(resolve => {
                    if (item.isFile) {
                        item.file(file => {
                            file.file = file.name;
                            folder.push(file);
                            resolve(file);
                        });
                    } else if (item.isDirectory) {
                        let dirReader = item.createReader();
                        dirReader.readEntries(entries => {
                            let entriesPromises = [];
                            subfolder = [];
                            folder.push({ folder: item.name, subfolder });
                            for (let entr of entries)
                                entriesPromises.push(
                                     traverseFileTreePromise(
                                        entr,
                                        path + item.name + "/", // Update the path here
                                        subfolder
                                    )
                                );
                            resolve(Promise.all(entriesPromises));
                        });
                    }
                });
            }

            async function getFilesDataTransferItems(dataTransferItems) {
                let files = [];
                return new Promise((resolve, reject) => {
                    let entriesPromises = [];
                    for (let it of dataTransferItems)
                        entriesPromises.push(
                            traverseFileTreePromise(it.webkitGetAsEntry(), "", files) // Pass an empty string as initial path
                        );
                    Promise.all(entriesPromises).then(entries => {
                        resolve(files);
                    });
                });
            }

            function getFilePaths(files) {
                let paths = [];
                
                function traverseFiles(files, currentPath = "") {
                    let hasFiles = false;
                    
                    for (let file of files) {
                        if (file.file) {
                            paths.push(currentPath + file.file);
                            hasFiles = true;
                        } else if (file.folder && file.subfolder) {
                            let folderPath = currentPath + file.folder + "/";
                            let subfolderHasFiles = traverseFiles(file.subfolder, folderPath);
                            
                            if (subfolderHasFiles || file.subfolder.length > 0) {
                                hasFiles = true;
                            }
                            
                            if (!hasFiles) {
                                // Push the folder path only if it hasn't been added before
                                if (!paths.includes(folderPath)) {
                                    paths.push(folderPath);
                                }
                            }
                        }
                    } 
                    return hasFiles; // Return whether files were found in this folder or not
                }     
                traverseFiles(files);
                return paths;
            }
            
            function handleFiles(files, paths) {
                console.log(paths);
                const formData = new FormData();
                formData.append("location", "{{ base64_encode($origin) }}");
                formData.append("filePath", paths);
                files.forEach(file => formData.append('files[]', file));

                fetch('{{ route("adminuser.documents.upload") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('upload-modal').style.display = 'none';
                    showNotification(data.message);
                });
            }

            function handleEntry(entry, currentPath = '') {
                const fullPath = currentPath + entry.name;
                if (entry.isFile) {
                    entry.file(file => {
                        handleFiles([file], fullPath);
                    });
                } else if (entry.isDirectory) {
                    handleFiles([], fullPath);
                    const directoryReader = entry.createReader();
                    directoryReader.readEntries(entries => {
                        for (const subEntry of entries) {
                            handleEntry(subEntry, fullPath + '/');
                        }
                    });
                }
            }

            event.preventDefault();
            const items = event.dataTransfer.items;

            getFilesDataTransferItems(items).then(files => {
                var paths = getFilePaths(files).join(",");
                var path = paths.toString();
                var formData = new FormData();

                formData.append('paths', path);
                formData.append('location', "{{ base64_encode($origin) }}");

                // Changed route and csrf_token placeholders to their actual values
                fetch('{{ route("adminuser.documents.multiupload") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    // Handle response data here
                })
                .catch(error => {
                    console.error('Error occurred:', error);
                    // Handle error response here
                });
            });

            for (const item of items) {
                const entry = item.webkitGetAsEntry();
                handleEntry(entry);
            }
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

        function handleFileSelection(input) {
            for (let i = 0; i < input.files.length; i++) {
                files.push(input.files[i]);
            }

            displayFileData(files);

            document.getElementById('upload-preview-modal').style.display='block';
            document.getElementById('upload-modal').style.display='none';

            $('#uploadFileSubmit').on('click', function(e) {
                if (a == 0) {
                    a = 1;
                    e.preventDefault();
                    $('.removeFileButton').html('<i class="fas fa-circle-notch fa-spin"></i>');
                    $('.removeFileButton').prop("disabled", true);
                    $('#uploadFileSubmit').prop("disabled", true);
                    $('.cancel-btn').prop("disabled", true);
                    $('.modal-close').prop("disabled", true);
                    $("#uploadFileSubmit").removeClass("upload-btn");
                    $('#browseFiles').hide();
                    $('#clearFiles').hide();

                    console.log(files);

                    const formData = new FormData();
                    formData.append("location", "{{ base64_encode($origin) }}");
                    files.forEach(file => formData.append('files[]', file));

                    fetch('{{ route("adminuser.documents.upload") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('upload-preview-modal').style.display = 'none';
                        showNotification(data.message);
                    });
                    }
            });
        }

        function displayFileData() {
            const tableBody = document.getElementById('upload-preview-list');
            tableBody.innerHTML = '';

            console.log(files);

            files.forEach((file, index) => {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${file.name}</td>
                    <td>${convertByte(file.size)}</td>
                    <td><button onclick="removeFile(${index})" id="removeFileButton" class="removeFileButton"><i class="fa fa-times"></i></button></td>
                `;
                tableBody.appendChild(newRow);
            });
        }

        function removeFile(index) {
            files.splice(index, 1); 
            displayFileData(files);
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
            document.getElementById('permission-modal').style.display='block';
            document.getElementById('permission-file-list-table').style.display="none";
            $('#setPermissionButton').prop("disabled", true);


            $('.setPermissionBox').on('click', function(e) {
                var checkboxStatusArray = [];

                $("#fileList input[type='checkbox']").each(function() {
                    var checkboxId = $(this).attr("id");
                    var isChecked = $(this).prop("checked");
                    var checkboxStatus = {
                        id: checkboxId,
                        checked: isChecked
                    };
                    checkboxStatusArray.push(checkboxStatus);
                });

                var formData = new FormData();
                
                
                checkboxStatusArray.forEach(function(checkboxStatus) {
                    formData.append(checkboxStatus.id, checkboxStatus.checked);
                });
                formData.append('userid', $('#IDuser').val());

                console.log(checkboxStatusArray);
                    fetch('{{ route("adminuser.documents.setpermission") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                });
            });
        }

        function savePermission() {
            document.getElementById('permission-modal').style.display='none';
            showNotification('Permission settings saved');
        }

        function checkUserPermission(user,role) {
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

            var formData = new FormData();
            formData.append('userid', user);

            fetch('{{ route("adminuser.documents.checkpermission") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(response => response.json())
            .then(data => {
                $('#permissionUser').text(data.username);

                data.permissionlist.forEach(function(permissionData) {
                    if (permissionData.permission == 1){
                        $("#" + permissionData.fileid).prop("checked", true);
                    }   
                });
            });
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

        function deleteFile(file) {
            document.getElementById('delete-file-modal').style.display='block';

            $('#deleteFileSubmit').on('click', function(e) {
                e.preventDefault();
                var formData = new FormData();

                formData.append('file', file);
                
                fetch('{{ route("adminuser.documents.deletefile") }}', {
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

        function renameFolder(folder, index, name) {
            document.getElementById('rename-folder-modal').style.display='block';
            $("#folder-index").attr("value", index);
            $("#newFolderName").attr("value", name);

            $('#renameFolderSubmit').on('click', function(e) {
                console.log('ts')
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
                
                formData.append('folder', folder);

                fetch('{{ route("adminuser.documents.deletefolder") }}', {
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
    </script>
    @endpush
@endsection