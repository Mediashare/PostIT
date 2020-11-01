function CodeMirorInit(markdownify_path, content) {
    var myCodeMirror = CodeMirror(document.getElementById('codemirror'), {
        value: content,
        mode:  "markdown",
        lineNumbers: true,
        lineWrapping: true,
        extraKeys: {"Ctrl-Space": "autocomplete"},
        keyMap: "sublime"
    });

    $('#post_content').height('calc(100vh - ' + $('#form_inputs').height() + 'px)');
    myCodeMirror.setSize('100%', 'calc(100vh - ' + $('#form_inputs').height() + 'px)');
    $('textarea#content_mirror').val(myCodeMirror.getValue());

    myCodeMirror.on('change', function (CodeMirror) {
        refreshPreview(markdownify_path, CodeMirror.getValue());
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

// TextArea to Markdown
function refreshPreview(markdownify_path, content) {
    $('textarea#content_mirror').val(content);
    $.post(markdownify_path, {content: content}, function (data) {
        $('.markdown-preview').html(data);
        document.querySelectorAll('.markdown-body pre code').forEach((block) => {
            hljs.highlightBlock(block);
        });
    });
}