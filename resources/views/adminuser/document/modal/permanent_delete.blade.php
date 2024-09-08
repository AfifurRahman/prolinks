<style>
    
</style>

<div id="permanent-delete-modal" class="modal">
    <div class="modal-content">
        <div class="modal-topbar">
            <div class="upload-modal-title">
                <h5 class="modal-delete-file-title">Delete permanently item</h5>
            </div>
            <button class="modal-close" onclick="document.getElementById('permanent-delete-modal').style.display='none'">
                <image class="modal-close-ico" src="{{ url('template/images/icon_menu/close.png') }}"></image>
            </button>
        </div>
        <div class="modal-body">
            <p class="modal-text">This will permanently remove the items and you cannot recover it, are you sure you want to continue?</p>
            <div class="form-button">
                <a onclick="document.getElementById('permanent-delete-modal').style.display='none'" class="cancel-btn">Cancel</a>
                <button class="delete-btn" id="deleteItemSubmit">Delete</button>
            </div>
        </div>
    </div>
</div>