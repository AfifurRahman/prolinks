<!DOCTYPE html>
<html>
<head>
    <title>Watermark PDF</title>
</head>
<body>
    <form action="{{ route('adminuser.documents.watermarkdownload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="pdf">Upload PDF:</label>
            <input type="file" name="pdf" required>
        </div>
        <div>
            <label for="watermark">Watermark Text:</label>
            <textarea name="watermark" rows="4" required></textarea>
        </div>
        <div>
            <button type="submit">Add Watermark</button>
        </div>
    </form>
</body>
</html>
