<style>
    .borderless td, .borderless th {
        border: none !important;
        width:5%;
    }

    .watermark-setting {
        display:flex;
        width:100%;
    }

    .setting {
        width:50%;
    }

    .preview {
        width:50%;
    }
</style>
<div class="watermark-setting">
    <div class="setting">
        <p class="text-header">Watermarks settings</p>
        <table class="table borderless">
            <tr>
                <th>Display</th>
                <td>
                    <input type="checkbox" class="setting-watermark-checkbox" id="viewing">&nbsp;&nbsp;Viewing</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="printing">&nbsp;&nbsp;Printing</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="downloading">&nbsp;&nbsp;Downloading PDF</input><br>
                </td>
            </tr>
            <tr>
                <th>Details</th>
                <td>
                    <input type="checkbox" class="setting-watermark-checkbox" id="projectname">&nbsp;&nbsp;Project name</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="fullname">&nbsp;&nbsp;Full name</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="email">&nbsp;&nbsp;Email</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="companyname">&nbsp;&nbsp;Company name</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="timestamp">&nbsp;&nbsp;Timestamp</input><br>
                </td>
            </tr>
            <tr>
                <th>Color</th>
                <td>
                    <input type="radio" checked>&nbsp;&nbsp;Gray</input>
                    <input type="radio">&nbsp;&nbsp;Blue</input>
                    <input type="radio">&nbsp;&nbsp;Orange</input>
                    <input type="radio">&nbsp;&nbsp;Red</input>
                </td>
            </tr>
            <tr>
                <th>Opacity</th>
                <td>
                    <input type="range" style="width:260px;"></input>
                </td>
            </tr>
            <tr>
                <th>Position</th>
                <td>
                    <input type="radio" checked>&nbsp;&nbsp;Center</input>
                </td>
            </tr>
        </table>
        <div class="watermark-setting-button">
            <button>Save changes</button>
        </div>
    </div>
    <div class="preview">
        <p class="text-header">Preview</p>
    </div>
</div>