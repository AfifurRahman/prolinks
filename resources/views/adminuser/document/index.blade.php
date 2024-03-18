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
                        <h5 class="modal-title-text">Upload file or folder</h5>
                    </div>
                    <button class="modal-close" onclick="document.getElementById('upload-modal').style.display='none'">
                        <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                    </button>
                </div>
                <div class="drag-area" id="dragArea" ondrop="handleDrop(event)">
                    <span class="header">Drag & Drop</span>
                    <span class="header">or <span class="button" onclick="document.getElementById('fileInput').click();">browse</span></span>
                    <input id="fileInput" type="file" style="visibility:hidden;position:absolute;" multiple onchange="handleFileSelection(this)">
                    <span class="support">Supports jpg, jpeg, png, doc, docx, ppt, pptx, xlsx, pdf, and zip</span>
                    <progress id="uploadProgress" value="50" max="100"></progress>
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
                        @if($origin == "")
                            <input name="location" type="hidden" value=""></input>
                        @else
                            <input name="location" type="hidden" value="{{ base64_encode($origin.'/') }}"></input>
                        @endif
                        <div class="form-button">
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
                        <label>Folder name</label>
                        <input type="text" class="form-control" name="new_name" id="folder_name"></input>
                        <input type="hidden" id="old-name" name="old_name" value=""></input>
                        <div class="form-button">
                            <button class="create-btn" type="submit">Rename Folder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="box_helper">
            <h2 id="title" style="color:black;font-size:28px;">Documents</h2>
            <div class="button_helper">
                <button class="export">Export</button>
                <button class="createfolder" onclick="document.getElementById('create-folder-modal').style.display='block'">Create folder</button>

                <button class="upload" onclick="document.getElementById('upload-modal').style.display='block'"><image class="upload_ico" src="{{ url('template/images/icon_menu/upload.png') }}" ></image>Upload</button>
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
                <image class="search_icon" src="{{ url('template/images/icon_menu/search.png') }}"></image>
                <input type="text" class="searchbar" placeholder="Search documents...">
            </div>
        </div>
        
        <div class="tableContainer">
            <table class="tableDocument">
                <thead>
                    <tr>
                        <th id="index">Index</th>
                        <th id="name">File name</th>
                        <th id="created">Created at</th>
                        <th id="size">Size / type</th>
                        <th id="navigationdot">&nbsp;</th>
                    </tr>
                </thead>
                @if(!$origin == "")
                    <tr>
                        <td></td>
                        <td>
                            <a class="fol-fil" href="{{ route('adminuser.documents.folder', base64_encode(($pos = strpos($origin, '/')) !== false ? substr($origin, 0, $pos) : '')) }}">
                                <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                                ...
                            </a>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif
                @foreach ($folders as $index => $directory)
                    @if(DB::table('upload_folders')->where('basename', basename($directory))->value('status') == 1)
                        <tr>
                            <td>{{ $index + 1 }}</td>
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
                                        <li><a onclick="rename('{{ base64_encode(basename($directory)) }}')">Edit folder</a></li>
                                        <li><a href="{{ route('adminuser.documents.delete_folder', base64_encode(basename($directory))) }}">Delete folder</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
                @foreach ($files as $index => $file)
                    @if(DB::table('upload_files')->where('basename', basename($file))->value('status') == 1)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a class="fol-fil" href="{{ route('adminuser.documents.file', [base64_encode($origin.'/'.basename($file)), base64_encode(basename($file))] ) }}">
                                
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
            // Handle drag and drop file
            function handleDrop(event) {
                event.preventDefault();
                uploadProgress.style.display = 'block';

                const items = event.dataTransfer.items;
                for (const item of items) {
                    const entry = item.webkitGetAsEntry();
                    if (entry.isFile) {
                        handleFiles([item.getAsFile()]);
                    } else if (entry.isDirectory) {
                        handleFolder(entry, entry.name + "/");
                    }
                }
            }
            //handle pop up file
            function handleFileSelection(input) {
                if (input.files && input.files.length > 0) {
                    const selectedFiles = [];
                    for (let i = 0; i < input.files.length; i++) {
                        selectedFiles.push(input.files[i]);
                    }
                    // Process the selected files
                    handleFiles(selectedFiles);
                } 
            }

            // If uploaded is file
            function handleFiles(files) {
                const formData = new FormData();
                formData.append("location", "{{ base64_encode($origin.'/') }}");
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
                        uploadProgress.style.display = 'none';
                        throw new Error('Failed to upload the file, unsupported file type');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        showNotification("File successfully uploaded");
                        uploadProgress.style.display = 'none';
                    }
                });
            }

            function handleFolder(item, path) {
                fetch('{{ route("adminuser.documents.uploadfolder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ folder_name: item.name, location: "{{ base64_encode($origin.'/') }}" })
                })
                .then(response => response.json());

                showNotification("File successfully uploaded");
            }

            document.addEventListener("DOMContentLoaded", function() {
                const dragArea = document.getElementById('dragArea');
                const uploadProgress = document.getElementById('uploadProgress');

                uploadProgress.style.display = 'none';

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
                    uploadProgress.style.display = 'block';
                });
            });

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
