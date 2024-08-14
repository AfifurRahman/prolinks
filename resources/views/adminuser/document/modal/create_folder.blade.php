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
            <div class="create-folder-input">
                <image class="create-folder-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                <input type="text" class="form-control" id="folderName" value="New folder"></input>
            </div>
            <div class="form-button">
                <a onclick="document.getElementById('create-folder-modal').style.display='none'" class="cancel-btn">Cancel</a>
                <button class="create-btn" id="createFolderSubmit">Create Folder</button>
            </div>
        </div>
    </div>
</div>