<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ DB::table('upload_files')->where('basename', basename($file))->value('name') }}</title>
    <script type="text/javascript" src="https://cdn.rawgit.com/asvd/dragscroll/master/dragscroll.js"></script>
    <link href="{{ url('clientuser/documentview.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="navigationBar">
        <div class="actionBar">
            <div class="titleBar">
                <img class="icon1" src="{{ url('template/images/icon_menu/pdf.png') }}" alt="Menu Icon" />
                <div>
                    {{ DB::table('upload_files')->where('basename', basename($file))->value('name') }}
                </div>
            </div>
            <img class="bar" src="{{ url('template/images/icon_menu/bar.png') }}" />
            <div class="buttonAction">
                <a class="action1" href="{{ route('adminuser.documents.downloadfile', base64_encode(basename($file))) }}">
                    <img class="icon" src="{{ url('template/images/icon_menu/download.png') }}" alt="Download" />
                </a>
            </div>
        </div>
        <div class="closeAction">
            <a href="{{ route('adminuser.documents.list', base64_encode($link)) }}">
                <img class="icon" src="{{ url('template/images/icon_menu/close.png') }}" alt="Close" />
            </a>
        </div>
    </div>
    <div class="PDFframe">
        <iframe src="{{ route('adminuser.documents.serve', ['file' => base64_encode($file)]) }}#toolbar=0" type="application/pdf">
    </div>
</body>
