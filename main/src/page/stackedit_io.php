<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StackEdit Integration Example</title>
    <style>
        #stackedit-iframe {
            width: 100%;
            height: 1000px;
            border: none;
        }
        #save-btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <iframe id="stackedit-iframe" src="https://stackedit.io/app"></iframe>
    <button id="save-btn">Save File</button>

    <script>
        document.getElementById('save-btn').addEventListener('click', function() {
            const stackeditFrame = document.getElementById('stackedit-iframe');
            stackeditFrame.contentWindow.postMessage({
                type: 'stackedit-save-file'
            }, '*');
        });

        window.addEventListener('message', function(event) {
            if (event.origin !== 'https://stackedit.io') {
                return;
            }

            const message = event.data;

            if (message.type === 'stackedit-file-saved') {
                const markdownContent = message.content.text;

                const blob = new Blob([markdownContent], { type: 'text/markdown;charset=utf-8' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'content.md';
                link.click();
            }
        });
    </script>
</body>
</html>
