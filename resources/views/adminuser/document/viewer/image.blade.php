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
                <img class="icon1" src="{{ url('template/images/icon_menu/png.png') }}" alt="Menu Icon" />
                <div>
                    {{ DB::table('upload_files')->where('basename', basename($file))->value('name') }}
                </div>
            </div>
            <img class="bar" src="{{ url('template/images/icon_menu/bar.png') }}" />
            <div class="zoomAction">
                <a class="action" onclick="zoomin()">
                    <img class="icon" src="{{ url('template/images/icon_menu/zoom-in.png') }}" />
                </a>
                <a class="action" onclick="zoomout()">
                    <img class="icon" src="{{ url('template/images/icon_menu/zoom-out.png') }}" />
                </a>
            </div>
            <img class="bar" src="{{ url('template/images/icon_menu/bar.png') }}" />
            <div class="buttonAction">
                <a class="action1" href="{{ route('adminuser.documents.downloadfile', base64_encode(basename($file))) }}">
                    <img class="icon" src="{{ url('template/images/icon_menu/download.png') }}" alt="Download" />
                </a>
                <a class="action1" onclick="printDiv()">
                    <img class="icon" src="{{ url('template/images/icon_menu/printer.png') }}" alt="Print" />
                </a>
            </div>
        </div>
        <div class="closeAction">
            <a href="{{ route('adminuser.documents.openfolder', base64_encode($link)) }}">
                <img class="icon" src="{{ url('template/images/icon_menu/close.png') }}" alt="Close" />
            </a>
        </div>
    </div>
    <div id="displayImage">
        <div class="main dragscroll">
            <img id="Image" class="imageViewer" src="{{ route('adminuser.documents.serve', ['file' => base64_encode($file)]) }}" alt="PDF Viewer">
        </div>
    </div>
</body>

<script>
        let img;
        let width;

        window.onload = function() {
            img = document.getElementById('Image');
            width = img.width;
        };

        function zoomin() {
            var myImg = document.getElementById("Image");
            var currWidth = myImg.clientWidth;
            myImg.style.width = (currWidth + 100) + "px";
        }

        function zoomout() {
            var myImg = document.getElementById("Image");
            var currWidth = myImg.clientWidth;
            myImg.style.width = (currWidth - 100) + "px";
        }

        function printDiv() {
            img.style.width = width + "px";
            var printContents = document.getElementById("displayImage").innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
</html>
