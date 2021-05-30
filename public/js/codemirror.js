function CodeMirorInit(render_path, content, mode = 'markdown') {
    var myCodeMirror = CodeMirror(document.getElementById('codemirror'), {
        value: content,
        mode:  mode,
        lineNumbers: true,
        lineWrapping: true,
        extraKeys: {"Ctrl-Space": "autocomplete"},
        keyMap: "sublime"
    });

    $('#editor_preview').height('calc(100vh - ' + $('#form_header').height() + 'px)');
    myCodeMirror.setSize('100%', 'calc(100vh - ' + $('#form_header').height() + 'px)');
    $('textarea#content_mirror').val(myCodeMirror.getValue());

    myCodeMirror.on('change', function (CodeMirror) {
        renderPreview(render_path, CodeMirror.getValue());
    });
    
    // TextArea Macro
    $(window).bind('keydown', function(event) {
        if (event.ctrlKey || event.metaKey) {
            switch (String.fromCharCode(event.which).toLowerCase()) {
            case 's':
                event.preventDefault();
                $('form button.btn-submit, form button.btn-submit').click();
                break;
            }
        }
    });

    return myCodeMirror;
}

var renderPreview = false;

// Render TextArea Content
function renderPreview(render_path, content) {
    if (renderPreview === false) {
        renderPreview = true;
        $('textarea#content_mirror').val(content);
        $.post(render_path, {content: content}, function (data) {
            $('.render-preview').html(data);
            document.querySelectorAll('.markdown-body pre code').forEach((block) => {
                Prism.highlightElement(block);
            });
            renderPreview = false;
        });
    }
}
