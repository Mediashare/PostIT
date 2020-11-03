function CodeMirorInit(selector = 'codemirror', content, mode = 'markdown', render_path) {
    var myCodeMirror = CodeMirror(document.getElementById('textarea_' + selector), {
        value: content,
        mode:  mode,
        lineNumbers: true,
        lineWrapping: true,
        extraKeys: {"Ctrl-Space": "autocomplete"},
        keyMap: "sublime"
    });

    $('#preview_' + selector).height('calc(100vh - ' + $('#form_header').height() + 'px)');
    myCodeMirror.setSize('100%', 'calc(100vh - ' + $('#form_header').height() + 'px)');
    $('textarea#mirror_' + selector).val(myCodeMirror.getValue());

    myCodeMirror.on('change', function (CodeMirror) {
        renderPreview(render_path, CodeMirror.getValue(), selector);
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

// Render TextArea Content
function renderPreview(render_path, content, selector) {
    $.post(render_path, {content: content}, function (data) {
        $('.render_' + selector).html(data);
        document.querySelectorAll('.markdown-body pre code').forEach((block) => {
            Prism.highlightElement(block);
        });
    });
}