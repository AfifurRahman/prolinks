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