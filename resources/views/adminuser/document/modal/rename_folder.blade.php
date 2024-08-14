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
                    <div class="rename-folder-input">
                        <image class="rename-folder-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                        <input type="text" class="form-control" id="newFolderName" placeholder="Enter folder name"/>
                    </div>
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