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