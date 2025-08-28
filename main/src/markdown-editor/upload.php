<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Article</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Upload Article</h2>
        <form action="save_article.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="code" class="form-label">codeวิชา</label>
                <input type="text" class="form-control" id="code" name="code" required>
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">ชื่อวิชา</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="mb-3">
                <label for="chapter" class="form-label">บทที่</label>
                <input type="number" class="form-control" id="chapter" name="chapter" required>
            </div>
            <div class="mb-3">
                <label for="file" class="form-label">ไฟล์ .md</label>
                <input type="file" class="form-control" id="file" name="file" accept=".md" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</body>
</html>
