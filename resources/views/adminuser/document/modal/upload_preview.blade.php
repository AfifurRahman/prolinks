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