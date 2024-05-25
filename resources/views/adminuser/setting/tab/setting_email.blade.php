<style>
    .borderless td, .borderless th {
        border: none !important;
    }
</style>
<form action="{{ route('setting.save-setting-email') }}" method="post">
    @csrf
    <table class="table borderless">
        <tr>
            <th width="300">Upload File Notification</th>
            <td>
                <input type="checkbox" id="email-upload-file" {{ !empty($setting->is_upload_file) && $setting->is_upload_file == 1 ? 'checked':'' }} switch="primary" name="is_upload_file" />
                <label for="email-upload-file" data-on-label="On" data-off-label="Off"></label>
            </td>
        </tr>
        <tr>
            <th>Q&A Notification</th>
            <td>
                <input type="checkbox" id="email-qna" {{ !empty($setting->is_discussion) && $setting->is_discussion == 1 ? 'checked':'' }} switch="primary" name="is_discussion" />
                <label for="email-qna" data-on-label="On" data-off-label="Off"></label>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fa fa-save"></i> Save</button>
            </td>
        </tr>
    </table>
</form>