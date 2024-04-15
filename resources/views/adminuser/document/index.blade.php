@extends('layouts.app_client')

<link href="{{ url('clientuser/documentindex.css') }}" rel="stylesheet" type="text/css" />

@section('notification')
    <div class="notificationlayer">
        <div class="notification">
            <image class="notificationicon" src="{{ url('template/images/icon_menu/checklist.png') }}"></image>
            <p class="notificationtext"></p>
        </div>
    </div>
@endsection

@section('content')
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
                    <button class="modal-upload-btn" onclick="document.getElementById('fileInput').click();">Browse</button>
                    <input id="fileInput" type="file" style="visibility:hidden;position:absolute;" multiple onchange="handleFileSelection(this)">
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
                <button class="modal-close" onclick="document.getElementById('create-folder-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('adminuser.documents.rename_folder') }}" method="POST">
                    @csrf
                    <label>Index</label>
                    <input type="text" class="form-control"></input>
                    <label>File name</label>
                    <input type="text" class="form-control" name="new_name" id="folder_name"></input>
                    <input type="hidden" id="old-name" name="old_name" value=""></input>
                    <div class="form-button">
                        <button class="cancel-btn">Cancel</button>
                        <button class="create-btn" type="submit">Save changes</button>
                    </div>
                </form>
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
                <form action="{{ route('adminuser.documents.create_folder') }}" method="POST">
                    @csrf
                    <label>Folder name</label>
                    <input type="text" class="form-control" name="folder_name" id="folder_name"></input>
                    <input name="location" type="hidden" value="{{ base64_encode($origin) }}"></input>
                    <div class="form-button">
                        <button class="cancel-btn">Cancel</button>
                        <button class="create-btn" type="submit">Create Folder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Folder Name Modal -->
    <div id="rename-folder-modal" class="modal">
        <div class="modal-content">
            <div class="modal-topbar">
                <div class="upload-modal-title">
                    <h5 class="modal-title-text">Rename folder</h5>
                </div>
                <button class="modal-close" onclick="document.getElementById('create-folder-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('adminuser.documents.rename_folder') }}" method="POST">
                    @csrf
                    <label>Index</label>
                    <input type="text" class="form-control"></input>
                    <label>Folder name</label>
                    <input type="text" class="form-control" name="new_name" id="folder_name"></input>
                    <input type="hidden" id="old-name" name="old_name" value=""></input>
                    <div class="form-button">
                        <button class="cancel-btn">Cancel</button>
                        <button class="create-btn" type="submit">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

     <!-- Permission -->
     <div id="permission-modal" class="modal">
        <div class="modal-content">
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
                    <table id="permission-user-list-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($listusers) > 0) 
                                @foreach($listusers->unique('group_id') as $group)
                                    <tr>
                                        <td>
                                            <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/group.png') }}" />
                                        </td>
                                        <td>
                                            {{ DB::table('access_group')->where('group_id', $group->group_id)->value('group_name') }}
                                        </td>
                                    </tr>
                                    @foreach($listusers as $user)
                                        @if($user->group_id == $group->group_id)
                                            <tr>
                                                <td></td>
                                                <td>
                                                    {{ $user->email_address }}
                                                    <br>
                                                    @if($user->role == 0) 
                                                        Administrator
                                                    @elseif($user->role == 1)
                                                        Collaborator
                                                    @elseif($user->role == 2)
                                                        Client
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div> 
                <div class="permission-file-list">
                    <h4>Selected User - Role</h4>
                    <table id="permission-file-list-table">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th><input type="checkbox" class="checkbox" disabled/></th>
                            </tr>
                        </thead>
                    </table>
                </div> 
            </div>

            <div class="modal-footer">
                <button class="cancel-btn">Cancel</button>
                <button class="create-btn" type="submit">Save settings</button>
            </div>  
        </div>
    </div>

    <div class="box_helper">
        <h2 id="title" style="color:black;font-size:28px;">Documents</h2>
        <div class="button_helper">
            <button class="create-folder" onclick="document.getElementById('create-folder-modal').style.display='block'">Add folder</button>
            <button class="permissions" onclick="document.getElementById('permission-modal').style.display='block'"><image class="permissions-ico" src="{{ url('template/images/icon_menu/permissions.png') }}">Permissions</button>
            <button class="upload" onclick="document.getElementById('upload-modal').style.display='block'"><image class="upload-ico" src="{{ url('template/images/icon_menu/upload.png') }}"></image>Upload</button>
        </div>
    </div>


    <div class="box_helper">
        <div>
            <button class="filter_button">
                <image class="filter_icon" src="{{ url('template/images/icon_menu/filter.png') }}"></image>
                Filter
            </button>
        </div>
        <div class="searchbox">
                <img class="search_icon" src="{{ url('template/images/icon_menu/search.png') }}">
                <input type="text" name="name" class="searchbar" id="searchInput" placeholder="Search documents...">
        </div>
    </div>
    
    <div class="tableContainer">
        <table class="tableDocument">
            <thead>
                <tr>
                    <th id="check"><input type="checkbox" class="checkbox" disabled/></th>
                    <th id="index">Index</th>
                    <th id="name">File name</th>
                    <th id="created">Created at</th>
                    <th id="size">Size / type</th>
                    <th id="navigationdot">&nbsp;</th>
                </tr>
            </thead>
            @if($directorytype == 0)
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <a class="fol-fil" href="{{ route('adminuser.documents.folder', base64_encode(substr($origin,0,-9))) }}">
                            <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                            ...
                        </a>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif

            @php $index = 0; @endphp
            @foreach ($folders as $directory)
                @if(DB::table('upload_folders')->where('basename', basename($directory))->value('status') == 1)
                    @php $index++; @endphp
                    <tr>
                        <td><input type="checkbox" class="checkbox" /></td>
                        <td>{{ $index }}</td>
                        <td>
                            @if($origin == "")
                                <a class="fol-fil" href="{{ route('adminuser.documents.folder', base64_encode(basename($directory))) }}">
                                    <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                                    {{ DB::table('upload_folders')->where('basename', basename($directory))->value('name') }}
                                </a>
                            @else
                                <a class="fol-fil" href="{{ route('adminuser.documents.folder', base64_encode($origin.'/'.basename($directory))) }}">
                                    <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                                    {{ DB::table('upload_folders')->where('basename', basename($directory))->value('name') }}
                                </a>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified($directory))->format('d M Y, H:i') }}</td>
                        <td>Directory</td>
                        <td>
                            <div class="dropdown">
                                <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-top pull-right">
                                    <li><a onclick="rename('{{ base64_encode(basename($directory)) }}')">Rename folder</a></li>
                                    <li><a href="{{ route('adminuser.documents.delete_folder', base64_encode(basename($directory))) }}">Delete folder</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach

            @foreach ($files as $file)
                @if(DB::table('upload_files')->where('basename', basename($file))->value('status') == 1)
                    @php $index++; @endphp
                    <tr>
                        <td><input type="checkbox" class="checkbox" /></td>
                        <td>{{ $index }}</td>
                        <td>
                            <a class="fol-fil" href="{{ route('adminuser.documents.file', [ base64_encode($origin), base64_encode(basename($file)) ] ) }}">
                            <image class="file-icon" src="{{ url('template/images/icon_menu/' . pathinfo(DB::table('upload_files')->where('basename', basename($file))->value('name'), PATHINFO_EXTENSION) . '.png') }}" />
                                
                                {{ DB::table('upload_files')->where('basename',basename($file))->value('name') }}
                            </a>
                        </td>
                        <td>{{ date('d M Y, H:i', strtotime(DB::table('upload_files')->where('basename', basename($file))->value('created_at'))) }}</td>
                        <td>{{ App\Helpers\GlobalHelper::formatBytes(Storage::size($file)) }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-top pull-right">
                                    <li><a href="">Rename file</a></li>
                                    <li><a href="{{ route('adminuser.documents.delete_file', basename($file)) }}">Delete file</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>
    </div>

    @push('scripts')
    <script>
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
                "bSort": false,
                "dom": 'rtip',
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
                .then(response => {
                    if (!response.ok) {
                        //showNotification("Upload failed");
                        throw new Error('Failed to upload the file, unsupported file type');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    if (data.success) {
                       // showNotification("File successfully uploaded");
                    }
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
                fetch('{{ route("adminuser.documents.multiup") }}', {
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

        //handle pop up file
        function handleFileSelection(input) {
            if (input.files && input.files.length > 0) {
                const files = [];
                for (let i = 0; i < input.files.length; i++) {
                    files.push(input.files[i]);
                }
                // Process the selected files
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
                .then(response => {
                    if (!response.ok) {
                        showNotification("Upload failed");
                        throw new Error('Failed to upload the file, unsupported file type');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        showNotification("File successfully uploaded");
                    }
                });
            } 
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
        }

        function rename(folder) {
            document.getElementById('rename-folder-modal').style.display = 'block';
            document.getElementById('old-name').value = folder;
        }

    </script>
    @endpush
@endsection