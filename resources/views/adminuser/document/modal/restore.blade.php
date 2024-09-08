<style>
    
</style>

<div id="restore-item-modal" class="modal">
    <div class="modal-content">
        <div class="modal-topbar">
            <div class="upload-modal-title">
                <h5 class="modal-delete-file-title">Restore item</h5>
            </div>
            <button class="modal-close" onclick="document.getElementById('delete-file-modal').style.display='none'">
                <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
            </button>
        </div>
        <div class="modal-body">
            <p class="modal-text">This will restore the items to the original location, are you sure you want to continue?</p>
            <div class="form-button">
                <a onclick="document.getElementById('restore-item-modal').style.display='none'" class="cancel-btn">Cancel</a>
                <button class="delete-btn" id="restoreItemSubmit">Restore</button>
            </div>
        </div>
    </div>
</div>