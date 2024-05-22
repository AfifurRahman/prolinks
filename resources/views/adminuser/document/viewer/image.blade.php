<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer</title>
    <style>
        .pdf-viewer {
            width: 100%;
            height: 100vh;
        }
    </style>
</head>
<body>
    <img src="{{ route('adminuser.documents.downloadfile', base64_encode(basename($file))) }}">
</body>
</html>
