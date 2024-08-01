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
        <form id="watermark-settings-form" method="POST" action="{{ route('adminuser.watermark.save_settings') }}">
        @csrf
        <table class="table borderless">
            <tr>
                <th>Display</th>
                <td>
                    <input type="checkbox" class="setting-watermark-checkbox" id="viewing" name="view" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('display_view') == '1' ? 'checked' : ''  }}>&nbsp;&nbsp;Viewing</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="printing" name="printing" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('display_printing') == '1' ? 'checked' : ''  }}>&nbsp;&nbsp;Printing</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="downloading" name="download" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('display_download') == '1' ? 'checked' : ''  }}>&nbsp;&nbsp;Downloading PDF</input><br>
                </td>
            </tr>
            <tr>
                <th>Details</th>
                <td>
                    <input type="checkbox" class="setting-watermark-checkbox" id="projectname" name="projectname" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('details_projectname') == '1' ? 'checked' : ''  }}>&nbsp;&nbsp;Project name</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="fullname" name="fullname" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('details_fullname') == '1' ? 'checked' : ''  }}>&nbsp;&nbsp;Full name</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="email" name="email" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('details_email') == '1' ? 'checked' : ''  }}>&nbsp;&nbsp;Email</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="companyname" name="companyname" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('details_companyname') == '1' ? 'checked' : ''  }}>&nbsp;&nbsp;Company name</input><br>
                    <input type="checkbox" class="setting-watermark-checkbox" id="timestamp" name="timestamp" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('details_timestamp') == '1' ? 'checked' : ''  }}>&nbsp;&nbsp;Timestamp</input><br>
                </td>
            </tr>
            <tr>
                <th>Color</th>
                <td>
                    <input type="radio" name="color" value="gray" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('color') == 'gray' || is_null(DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('color')) ? 'checked' : ''  }}>&nbsp;&nbsp;Gray</input>
                    <input type="radio" name="color" value="blue" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('color') == 'blue' ? 'checked' : ''  }}>&nbsp;&nbsp;Blue</input>
                    <input type="radio" name="color" value="orange" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('color') == 'orange' ? 'checked' : ''  }}>&nbsp;&nbsp;Orange</input>
                    <input type="radio" name="color" value="red" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('color') == 'red' ? 'checked' : ''  }}>&nbsp;&nbsp;Red</input>
                </td>
            </tr>
            <tr>
                <th>Opacity</th>
                <td>
                    <input type="range" name="opacity" style="width:260px;" min="1" max="100" value="{{ is_null(DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('opacity')) ? 0 : DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('opacity') }}" step="1" oninput="this.nextElementSibling.value = this.value + '%'">&nbsp;&nbsp;
                    <output>{{ is_null(DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('opacity')) ? "0" : DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('opacity') }}%</output>
                </td>
            </tr>
            <tr>
                <th>Position</th>
                <td>
                    <input type="radio" name="position" value="1" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('position') == '1' || is_null(DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('position')) ? 'checked' : ''  }}>&nbsp;&nbsp;Center</input>
                    <input type="radio" name="position" value="2" {{ DB::table('watermark')->where('client_id', Auth::user()->client_id)->value('position') == '2' ? 'checked' : ''  }} disabled>&nbsp;&nbsp;Tile</input>
                </td>
            </tr>
        </table>
        <div class="watermark-setting-button">
            <button type="submit">Save changes</button>
        </div>
    </form>
    </div>
    <div class="preview">
        <!-- <p class="text-header">Preview</p> -->
    </div>
</div>