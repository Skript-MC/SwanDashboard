
const easyMDE = require('easymde');

const diff2html = require('diff2html');

const diff = require('diff');

const editorDiv = document.getElementById('editor');
if (editorDiv) {
    const editor = new easyMDE({
        element: editorDiv,
        spellChecker: false,
        autofocus: true,
        autosave: {
            enabled: true,
            delay: 1000,
            uniqueId: 'editor'
        },
      toolbar: ['bold', 'italic', 'quote', 'link', 'code', '|', 'preview', 'side-by-side', 'fullscreen', '|', 'guide']
    });
    editor.codemirror.on('change', () => updateDiff(editor));
}

function updateDiff(e) {
  const name = document.getElementById('name').value || 'Sans nom';
  const type = document.getElementById('type').value;
  const diff = window.diff.createTwoFilesPatch('Sans nom', name, 'Aliases:\n\n\n', `${e.value()}`);
  const diffJson = diff2html.parse(diff);
  document.getElementById('diff').innerHTML = diff2html.html(diffJson, {
    drawFileList: false
  });
}
