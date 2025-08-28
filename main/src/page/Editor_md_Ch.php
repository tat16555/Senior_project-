<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduInsightHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/editor.md/css/editormd.min.css">
</head>
<body>
    <div id="editor-md">
        <textarea style="display:none;"></textarea>
    </div>
    <button id="save-btn">Save File</button>
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/editor.md/editormd.min.js"></script>
    <script>
        $(function() {
            var editor = editormd("editor-md", {
                path : "https://cdn.jsdelivr.net/npm/editor.md/lib/",
                width: "100%",
                height: 640,
                markdown: "",
                codeFold: true,
                saveHTMLToTextarea: true,
                searchReplace: true,
                emoji: true,
                taskList: true,
                tocm: true,
                tex: true,
                flowChart: true,
                sequenceDiagram: true,
                imageUpload: true,
                imageFormats: ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
                imageUploadURL: "./php/upload.php"
            });

            $('#save-btn').click(function() {
                var markdownContent = editor.getMarkdown();
                var blob = new Blob([markdownContent], { type: "text/markdown;charset=utf-8" });
                var link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = "content.md";
                link.click();
            });
        });
    </script>
</body>
</html>
