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