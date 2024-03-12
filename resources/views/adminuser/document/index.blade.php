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
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-topbar">
                <div class="upload-modal-title">
                    <h5 class="modal-title-text">Upload file or folder</h5>
                </div>
                <button class="modal-close" onclick="document.getElementById('upload-modal').style.display='none'">
                    <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
                </button>
            </div>
            <div class="modal-body">
                <div class="drag-area" id="dragArea">
                    <span class="header">Drag & Drop</span>
                    <span class="header">or <span class="button" onclick="document.getElementById('fileInput').click();">browse</span></span>
                    <input id="fileInput" type="file" style="visibility:hidden;position:absolute;" multiple webkitdirectory mozdirectory msdirectory odirectory directory>
                    <span class="support">Supports jpg, jpeg, png, doc, docx, ppt, pptx, xlsx, pdf, and zip</span>
                    <progress id="uploadProgress" value="50" max="100"></progress>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Folder Modal -->
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

    <div class="box_helper">
        @if($origin == "")
            <h2 id="title" style="color:black;font-size:28px;">Documents</h2>
        @else
            <h2 id="title" style="color:black;font-size:28px;">{{$origin}}</h2>
        @endif
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

    <div class="table">
        <table class="tableDocument">
            <thead>
                <tr>
                    <th id="check">Index</th>
                    <th id="name">File name</th>
                    <th id="company">Created at</th>
                    <th id="role">Size / type</th>
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
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if($origin == "")
                            <a class="fol-fil" href="{{ route('adminuser.documents.folder', base64_encode(basename($directory))) }}">
                                <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                                {{ basename($directory) }}
                            </a>
                        @else
                            <a class="fol-fil" href="{{ route('adminuser.documents.folder', base64_encode($origin.'/'.basename($directory))) }}">
                                <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                                {{ basename($directory) }}
                            </a>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified($directory))->format('d M Y, H:i') }}</td>
                    <td>Directory</td>
                    <td></td>
                </tr>
            @endforeach
            @foreach ($files as $index => $file)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <a class="fol-fil" href="{{ route('adminuser.documents.file', [base64_encode($origin.'/'.basename($file)), base64_encode(basename($file))] ) }}">
                           
                        <image class="file-icon" src="{{ url('template/images/icon_menu/' . pathinfo(DB::table('upload_files')->where('basename', basename($file))->value('name'), PATHINFO_EXTENSION) . '.png') }}" />
                            
                            {{ DB::table('upload_files')->where('basename',basename($file))->value('name') }}
                        </a>
                    </td>
                    <td>{{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified($file))->format('d M Y, H:i') }}</td>
                    <td>{{ round(Storage::size($file) / 1024 / 1024, 2) }} MB</td>
                    <td></td>
                </tr>
            @endforeach
        </table>
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var dragArea = document.getElementById('dragArea');
            var fileInput = document.getElementById('fileInput');
            var progressBar = document.getElementById('uploadProgress');

            progressBar.style.display='none';

            dragArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                dragArea.classList.add('highlight');
            });

            dragArea.addEventListener('dragleave', function() {
                dragArea.classList.remove('highlight');
            });

            dragArea.addEventListener('drop', function(e) {
                e.preventDefault();
                dragArea.classList.remove('highlight');
                progressBar.style.display='block';

                var files = e.dataTransfer.files;
                handleFiles(files);
            });

            fileInput.addEventListener('change', function() {
                var files = fileInput.files;
                handleFiles(files);
            });
        });

        function showNotification(message) {
            document.getElementById('upload-modal').style.display='none';
            document.querySelector('.notificationtext').textContent = message;
            document.querySelector('.notificationlayer').style.display = 'block';
            setTimeout(() => {
                $('.notificationlayer').fadeOut();
            }, 2000);
            setTimeout(function() {
                location.reload();
            }, 2250);
        }

        function handleFiles(files) {
            var progressBar = document.getElementById('uploadProgress');
            var formData = new FormData();
            formData.append("location", "{{base64_encode($origin.'/')}}");
            for (var i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }

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
                    progressBar.style.display='none';
                    throw new Error('Failed to upload the file, unsupported file type');
                }
                return response.json();
            })
            .then(data => {
                console.log(data);
                if(data.success){
                    showNotification("File successfully uploaded");
                    progressBar.style.display='none';
                }
            })
            .catch(error => {
                console.error('There was an error!', error);
                progressBar.style.display='none';
            });
        }
    </script>
    @endpush
@endsection
