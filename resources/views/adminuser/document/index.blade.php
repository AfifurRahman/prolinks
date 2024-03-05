@extends('layouts.app_client')

<style>
    .table {
        border-radius:0px;
        overflow:auto;
    }

    .tableDocument thead th:first-child {
        border-top-left-radius: 10px;
    }
    
    .tableDocument thead th:last-child {
        border-top-right-radius: 10px;
    }

    .tableDocument {
        border-collapse: separate;
        border:1px solid #CED5DD;
        border-radius: 7px;
        width:100%;
    }

    .tableDocument th {
        padding: 17px 0px 15px 10px;
        border-bottom: 1px solid #D0D5DD;
        background: #F9FAFB;
        font-size:15px;
        font-weight:600;
    }

    .tableDocument td {
        padding: 8px 0px 6px 10px;
        border-bottom:1px solid #CED5DD;
        font-size:13.5px;
        color:black;
    }

    .tableDocument tbody tr:last-child td {
        border-bottom: none;
    }

    .box_helper{
        margin-bottom:10px;
        display:flex;
        width:100%;
        justify-content: space-between;
    }

    .filter_button{
        padding:7px 15px 6px 17px;
        background: #FFFFFF; 
        color:#546474;
        border:1px solid #EDF0F2;
        border-radius:10px;
    }

    .filter_icon{
        margin-top:-1px;
        margin-right:4px;
        height:23px;
        width:20px;
    }

    .searchbox{
        width:22%;
        padding:8px 10px 5px 12px;
        border:1px solid #CED5DD;
        border-radius: 8px;
    }

    .searchbar{
        border:none;
    }

    .search_icon{
        width:19px;
        height:19px;
        margin-top:-3px;
        margin-right:4px;
    }

    .createfolder {
        color:#0072EE;
        border:1px solid #EDF0F2;
        border-radius:9px;  
        height:38px;
        background:#FFFFFF;   
        margin-top:10px;
        margin-right:6px;
        padding:8px 19px 7px 14px;
    }

    .export {
        color:#586474;
        border:none;
        background:#FFFFFF;
        margin-top:10px;
        margin-right:6px;
        padding:8px 19px 7px 14px;   
    }

    .upload {
        color:#FFFFFF;
        border:none;
        border-radius:9px;
        height:38px;
        background:#0072EE;
        margin-top:10px;
        padding:8px 19px 7px 14px;
    }

    .upload_ico {
        height:19px;
        width:20px;
        margin-top:-2px;
        margin-left:4px;
        margin-right:12px;
    }

    .fol-fil-icon{
        height:16.5px;
        width:20px;
        margin-right:8px;
        margin-top:6px;
        margin-bottom:8px;
    }
    
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.65);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 3% auto;
        border: 1px solid #888;
        width: 35%;
    }

    .modal-topbar {
        display:flex;
        border-bottom: 1px solid #D0D5DD;
        background: #F9FAFB;
        justify-content: space-between;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
        padding:10px 14px 0px 20px;
    }

    .modal-close {
        border:none;
        background:none;
    }

    .modal-title-text{
        margin-top:5px;
        font-size:15px;
        font-weight:600;
    }

    .modal-close-ico{
        margin-top:-6px;
        margin-right:-8px;
        width:24px;
        height:24px;
    }

    .drag-area {
        height: 400px;
        border: 3px dashed #e0eafc;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        margin: 10px auto;
    }

    h3 {
        margin-bottom: 20px;
        font-weight: 500;
    }

    .drag-area .icon {
        font-size: 50px;
        color: #1683ff;
    }

    .drag-area .header {
        font-size: 20px;
        font-weight: 500;
        color: #34495e;
    }

    .drag-area .support {
        font-size: 12px;
        color: gray;
        margin: 10px 0 15px 0;
    }

    .drag-area .button {
        font-size: 20px;
        font-weight: 500;
        color: #1683ff;
        cursor: pointer;
    }

    .drag-area.active {
        border: 2px solid #1683ff;
    }

    .drag-area img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .drag-area.highlight {
        border-color: #1683ff;
        background-color: rgba(22, 131, 255, 0.1);
    }

    .notificationlayer {
        position: absolute;
        width:100%;
        height:50px;
        z-index: 2;
        pointer-events: none;
        display:none;
    }

    .notification {
        background-color: #FFFFFF;
        border: 2px solid #12B76A;
        border-radius: 8px;
        display: flex;
        color: #232933;
        margin: 50px auto;
        text-align: center;
        height: 48px;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        transition: top 0.5s ease;    
    }

    .notificationicon {
        width:20px;
        height:20px;
        margin-top:11px;
        margin-left:15px;
    }

    .notificationtext{
        margin-top:11px;
        margin-left:8px;
        margin-right:13px;
        font-size:14px;
    }

</style>

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
                
            <div class="modal-body">
                <div class="drag-area" id="dragArea">
                    <span class="header">Drag & Drop</span>
                    <span class="header">or <span class="button" onclick="document.getElementById('fileInput').click();">browse</span></span>
                    <input id="fileInput" type="file" style="visibility:hidden;position:absolute;" multiple webkitdirectory mozdirectory msdirectory odirectory directory>
                    <span class="support">Supports jpg, jpeg, png, doc, docx, ppt, pptx, xlsx, pdf, and zip</span>
                </div>
            </div>
        </div>      
    </div>


    <div class="box_helper">
        <h2 id="title" style="color:black;font-size:28px;">Documents</h2>
        <div class="button_helper">
            <button class="export">Export</button>
            <button class="createfolder">Create folder</button>  
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
                    <th id="role">Size</th> 
                    <th id="navigationdot">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <a class="fol-fil" href="#">
                            <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                            Videos
                        </a>
                    </td>
                    <td>11 Feb 2024, 19.38</td>
                    <td>26Mb</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var dragArea = document.getElementById('dragArea');
            var fileInput = document.getElementById('fileInput');

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
        }

        function handleFiles(files) {
            var formData = new FormData();
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
                    throw new Error('Failed to upload the file, unsupported file type');
                }
                return response.json();
            })
            .then(data => {
                console.log(data);
                if(data.success){
                    showNotification("File successfully uploaded");     
                }
            })
            .catch(error => {
                console.error('There was an error!', error);
            });
        }
    </script>
    @endpush
@endsection