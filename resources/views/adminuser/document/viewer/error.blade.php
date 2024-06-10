<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ DB::table('upload_files')->where('basename', basename($file))->value('name') }}</title>
    <link href="{{ url('clientuser/documentview.css') }}" rel="stylesheet" type="text/css" />
</head>
<style>
.pageContent {
    display: flex;
    height: 100%;
    width: 100%;
    justify-content: center;
    align-items: center;
}

.downloadPrompt {
    width: 380px;
    height: 180px;
    padding-top: 25px;
    background-color: #504c4c;
    opacity: 0.95;
    text-align: center;
    border-radius: 10px;
    border: 1px #FFFFFF solid;
    margin-top:250px;
}

.downloadButton{
    border:none;
    border-radius:10px;
    background:#1570EF;
    color:#FFFFFF;
    padding:6px 10px 6px 10px;
    text-decoration:none;
}

.promptText{
    color:white;
}

</style>
<body>
    <div class="navigationBar">
        <div class="actionBar">
            <div class="titleBar">
                <img class="icon1" src="{{ url('template/images/icon_menu/' . pathinfo(DB::table('upload_files')->where('basename', basename($file))->value('name'), PATHINFO_EXTENSION) . '.png') }}" alt="Menu Icon" />
                <div>
                    {{ DB::table('upload_files')->where('basename', basename($file))->value('name') }}
                </div>
            </div>
        </div>
        <div class="closeAction">
            <a href="{{ route('adminuser.documents.list', base64_encode($link)) }}">
                <img class="icon" src="{{ url('template/images/icon_menu/close.png') }}" alt="Close" />
            </a>
        </div>
    </div>
    <div class="pageContent">
        <div class="downloadPrompt">
            <p class="promptText">Preview isn't available for this file type</p>
            <p class="promptText">You still can download the file</p>
            <p>&nbsp;</p>
            <a href="{{ route('adminuser.documents.downloadfile', base64_encode(basename($file))) }}" class="downloadButton">Download File</a>
        </div>
    </div>
</body>
