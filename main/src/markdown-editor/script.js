const editor = document.getElementById('editor');
const preview = document.getElementById('preview');

// Initialize CodeMirror editor
const codeMirrorEditor = CodeMirror.fromTextArea(editor, {
    mode: 'markdown',
    lineNumbers: true,
    theme: 'default'
});

// Function to update the preview pane
function updatePreview() {
    const markdownText = codeMirrorEditor.getValue();
    preview.innerHTML = marked.parse(markdownText);
}

// Function to apply formatting
function applyFormatting(tag) {
    const selection = codeMirrorEditor.getSelection();
    const formattedText = `${tag}${selection}${tag}`;
    codeMirrorEditor.replaceSelection(formattedText);
    updatePreview();
}

// Function to save the file
function saveFile() {
    const markdownText = codeMirrorEditor.getValue();
    const blob = new Blob([markdownText], { type: 'text/markdown' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'document.md';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Event listeners for toolbar buttons
document.getElementById('bold').addEventListener('click', () => applyFormatting('**'));
document.getElementById('italic').addEventListener('click', () => applyFormatting('*'));
document.getElementById('underline').addEventListener('click', () => applyFormatting('__'));
document.getElementById('code').addEventListener('click', () => applyFormatting('`'));
document.getElementById('header').addEventListener('click', () => {
    const level = prompt('Enter header level (1-6):', '1');
    if (level >= 1 && level <= 6) {
        applyFormatting('#'.repeat(level) + ' ');
    }
});
document.getElementById('link').addEventListener('click', () => {
    const url = prompt('Enter URL:', 'http://');
    const text = prompt('Enter link text:', 'Link');
    if (url && text) {
        applyFormatting(`[${text}](${url})`);
    }
});
document.getElementById('list').addEventListener('click', () => applyFormatting('- '));
document.getElementById('quote').addEventListener('click', () => applyFormatting('> '));
document.getElementById('save').addEventListener('click', saveFile);

// Event listener to update preview on editor change
codeMirrorEditor.on('change', updatePreview);

// Initialize preview with empty content
updatePreview();
